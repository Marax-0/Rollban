'use client';

import { useEffect, useState, use, useCallback, useRef } from 'react';
import styles from './page.module.css';
import Image from 'next/image';
import { Hash, User } from 'lucide-react';
import { useNetworkError } from '../../../components/NetworkErrorProvider';
import { playGoogleTTS, THAI_VOICES } from '../../../lib/google-tts';

// ฟังก์ชันสำหรับซ่อนนามสกุล (แสดงแค่ 3 ตัวแรก + XXX)
const maskSurname = (surname: string | number | boolean | null | undefined): string => {
  if (!surname || surname === '-' || typeof surname !== 'string') return '-';
  if (surname.length <= 3) return surname;
  return surname.substring(0, 3) + 'XXX';
};

interface Setting {
  id: number;
  department: string;
  n_hospital: string;
  n_room: string;
  n_table: string;
  n_listtable: string;
  n_listroom: string;
  department_load: string;
  department_room_load: string;
  time_col: string;
  table_arr: string;
  table_arr2: string;
  amount_boxL: number;
  amount_boxR: number;
  stem_surname: string;
  stem_surname_table: string;
  stem_surname_popup: string;
  station_l: string;
  station_r: string;
  stem_popup: string;
  a_sound: string;
  b_sound: string;
  c_sound: string;
  stem_name: string | null;
  urgent_color: string;
  lock_position: string;
  lock_position_right: string;
  urgent_level: string;
  status_patient: string;
  status_check: string;
  ads: string;
  timeout: string | null;
  pages: string | null;
  urgent_setup: string;
  type: string | null;
  alternate: string | null;
  voice: string | null;
  style_voice: string | null;
  set_descrip: string;
  set_notice: string;
  time_wait: string;
  listPage: string;
  limitNum: string | null;
  speedLoop: string | null;
  activeLoop: string | null;
}

interface VisitInfo {
  id?: number;
  code_dept_id?: string;
  patient_name?: string;
  queue_number?: string;
  visit_date?: string;
  status?: string;
  urgent_id?: number;
  urgent_color?: string;
  urgent_setup?: string;
  urgent_level?: string;
  [key: string]: string | number | boolean | null | undefined;
}

export default function SinglePage({ params }: { params: Promise<{ id: string }> }) {
  const [setting, setSetting] = useState<Setting | null>(null);
  const [visitData, setVisitData] = useState<VisitInfo[]>([]);
  const [activeData, setActiveData] = useState<VisitInfo[]>([]);
  const [callData, setCallData] = useState<VisitInfo | null>(null);
  const [showCallPopup, setShowCallPopup] = useState(false);
  const [skippedData, setSkippedData] = useState<VisitInfo[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  // Default setting object
  const [defaultSetting] = useState<Setting>({
    id: 0,
    department: 'ตรวจโรคทั่วไป',
    n_hospital: 'โรงพยาบาลชนบท',
    n_room: 'ห้องตรวจ',
    n_table: 'จุดซักประวัติ',
    n_listtable: '',
    n_listroom: '',
    department_load: '0',
    department_room_load: '0',
    time_col: 'false',
    table_arr: 'false',
    table_arr2: 'false',
    amount_boxL: 3,
    amount_boxR: 0,
    stem_surname: 'false',
    stem_surname_table: 'false',
    stem_surname_popup: 'false',
    station_l: 'โต๊ะซักประวัติ 1,โต๊ะซักประวัติ 2,โต๊ะซักประวัติ 3',
    station_r: '',
    stem_popup: 'false',
    a_sound: 'true',
    b_sound: 'false',
    c_sound: 'false',
    stem_name: null,
    urgent_color: 'true',
    lock_position: 'false',
    lock_position_right: 'false',
    urgent_level: 'false',
    status_patient: 'false',
    status_check: 'false',
    ads: '',
    timeout: null,
    pages: null,
    urgent_setup: '',
    type: null,
    alternate: null,
    voice: null,
    style_voice: null,
    set_descrip: 'true',
    set_notice: 'true',
    time_wait: '300',
    listPage: '',
    limitNum: null,
    speedLoop: null,
    activeLoop: null
  });
  
  // ใช้ network error context
  const { retryConnection } = useNetworkError();
  
  // ใช้ ref เพื่อหลีกเลี่ยง circular dependency
  const playTTSRef = useRef<(data: VisitInfo) => void>(() => {});
  const updateCallStatusRef = useRef<(vn: string) => void>(() => {});

  // ฟังก์ชันสำหรับจัดการ error
  const handleError = useCallback((error: unknown, context: string) => {
    console.error(`Error in ${context}:`, error);
    
    if (error instanceof Error) {
      // ตรวจสอบว่าเป็น timeout error หรือไม่
      const isTimeoutError = error.name === 'TimeoutError' || 
                           error.message.includes('timeout') ||
                           error.message.includes('AbortError');
      
      // ตรวจสอบว่าเป็น network error หรือไม่
      const isNetworkError = error.message.includes('Failed to fetch') || 
                           error.message.includes('NetworkError') ||
                           error.message.includes('fetch') ||
                           error.message.includes('TypeError');
      
      if (isTimeoutError || isNetworkError) {
        // Timeout หรือ Network error - ระบบจะจัดการผ่าน useNetworkStatus
        console.log(`${isTimeoutError ? 'Timeout' : 'Network'} error detected, handled by useNetworkStatus`);
      } else {
        // Server error - แสดง error message ตามที่ได้
        setError(error.message);
      }
    } else {
      setError('เกิดข้อผิดพลาดไม่ทราบสาเหตุ');
    }
  }, []);

  // ฟังก์ชันสำหรับ fetch ที่รองรับ timeout และ response error
  const fetchWithErrorHandling = useCallback(async (url: string, options: RequestInit = {}) => {
    try {
      const response = await fetch(url, {
        ...options,
        signal: AbortSignal.timeout(10000) // timeout 10 วินาที
      });

      if (response.ok) {
        return await response.json();
      } else {
        // มี response แต่เป็น error - แสดงข้อความจาก response
        const errorText = await response.text();
        let errorMessage = 'Server Error';
        try {
          const errorData = JSON.parse(errorText);
          errorMessage = errorData.error || errorData.message || `HTTP ${response.status}`;
        } catch {
          errorMessage = `HTTP ${response.status}: ${response.statusText}`;
        }
        throw new Error(errorMessage);
      }
    } catch (error) {
      if (error instanceof Error) {
        // ตรวจสอบว่าเป็น timeout error หรือไม่
        const isTimeoutError = error.name === 'TimeoutError' || 
                             error.message.includes('timeout') ||
                             error.message.includes('AbortError');
        
        if (isTimeoutError) {
          // Timeout error - ระบบจะจัดการผ่าน useNetworkStatus
          console.log('Timeout error detected, handled by useNetworkStatus');
          return null;
        } else {
          // Server error - throw error เพื่อให้ handleError จัดการ
          throw error;
        }
      } else {
        throw new Error('เกิดข้อผิดพลาดไม่ทราบสาเหตุ');
      }
    }
  }, []);

  // Unwrap params using React.use()
  const resolvedParams = use(params);
  const id = resolvedParams.id;

  const fetchVisitData = useCallback(async (departmentLoad: string) => {
    try {
      // Get current date in yyyy-mm-dd format
      const today = new Date().toISOString().split('T')[0];
      
      const result = await fetchWithErrorHandling('/api/data', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
          department_load: departmentLoad,
          visit_date: today
        }),
      });

      if (result && result.success) {
        setVisitData(result.data);
      } else if (result && !result.success) {
        console.error('Failed to fetch visit data:', result.error);
      }
    } catch (err) {
      handleError(err, 'fetchVisitData');
    }
  }, [handleError, fetchWithErrorHandling]);

  const fetchActiveData = useCallback(async (departmentLoad: string) => {
    try {
      // Get current date in yyyy-mm-dd format
      const today = new Date().toISOString().split('T')[0];
      
      const result = await fetchWithErrorHandling('/api/data/active', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
          department_load: departmentLoad,
          visit_date: today
        }),
      });

      if (result && result.success) {
        setActiveData(result.data);
      } else if (result && !result.success) {
        console.error('Failed to fetch active data:', result.error);
      }
    } catch (err) {
      handleError(err, 'fetchActiveData');
    }
  }, [handleError, fetchWithErrorHandling]);

  const fetchCallData = useCallback(async (departmentLoad: string) => {
    try {
      // Get current date in yyyy-mm-dd format
      const today = new Date().toISOString().split('T')[0];
      
      const result = await fetchWithErrorHandling('/api/data/call', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
          department_load: departmentLoad,
          visit_date: today
        }),
      });

      if (result && result.success && result.hasCall && result.data) {
        setCallData(result.data);
        setShowCallPopup(true);
        
        // Play TTS announcement
        if (playTTSRef.current) {
          playTTSRef.current(result.data);
        }
      } else if (result && !result.success) {
        console.error('Failed to fetch call data:', result.error);
        setCallData(null);
        setShowCallPopup(false);
      } else {
        setCallData(null);
        setShowCallPopup(false);
      }
    } catch (err) {
      handleError(err, 'fetchCallData');
    }
  }, [handleError, fetchWithErrorHandling]);

  const fetchSkippedData = useCallback(async (departmentLoad: string) => {
    try {
      // Get current date in yyyy-mm-dd format
      const today = new Date().toISOString().split('T')[0];
      
      const result = await fetchWithErrorHandling('/api/data/skipped', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
          department_load: departmentLoad,
          visit_date: today
        }),
      });

      if (result && result.success) {
        setSkippedData(result.data);
      } else if (result && !result.success) {
        console.error('Failed to fetch skipped data:', result.error);
        setSkippedData([]);
      }
    } catch (err) {
      handleError(err, 'fetchSkippedData');
    }
  }, [handleError, fetchWithErrorHandling]);

  const updateCallStatus = useCallback(async (vn: string) => {
    try {
      const result = await fetchWithErrorHandling('/api/data/call/update', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ vn }),
      });

      if (result && result.success) {
        console.log('Call status updated successfully:', result.message);
        // Refresh call data to check if there are more calls
        if (setting?.department_load) {
          fetchCallData(setting.department_load);
        }
      } else if (result && !result.success) {
        console.error('Failed to update call status:', result.message);
      }
    } catch (err) {
      handleError(err, 'updateCallStatus');
    }
  }, [setting?.department_load, fetchCallData, handleError, fetchWithErrorHandling]);

  const playTTS = useCallback(async (data: VisitInfo) => {
    try {
      const text = `,ขอเชิญหมายเลข  ${String(data.visit_q_no || '').split('').join(' ')}  ที่  ${data.station || 'จุดบริการ'} ค่ะ`;
      
      // ใช้ Google TTS แทน browser TTS
      await playGoogleTTS({
        text: text,
        language: 'th-TH',
        voice: THAI_VOICES.STANDARD_A, // เสียงผู้หญิงไทย
        speed: 0.8, // อ่านช้าๆ
        pitch: 1.0
      });
      
      // เมื่อ TTS พูดจบ ให้อัปเดต status_call = 2
      if (data.vn && updateCallStatusRef.current) {
        updateCallStatusRef.current(String(data.vn));
      }
      
      // ปิด popup หลังจาก TTS พูดจบ
      setTimeout(() => {
        setShowCallPopup(false);
      }, 500); // รอ 0.5 วินาทีหลังจาก TTS จบ
      
    } catch (error) {
      console.error('Error playing TTS:', error);
      
      // Fallback ไปใช้ browser TTS
      if ('speechSynthesis' in window) {
        const text = `,ขอเชิญหมายเลข  ${String(data.visit_q_no || '').split('').join(' ')}  ที่  ${data.station || 'จุดบริการ'} ค่ะ`;
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'th-TH';
        utterance.rate = 0.8;
        utterance.pitch = 1;
        
        utterance.onend = () => {
          if (data.vn && updateCallStatusRef.current) {
            updateCallStatusRef.current(String(data.vn));
          }
          setTimeout(() => {
            setShowCallPopup(false);
          }, 500);
        };
        
        speechSynthesis.speak(utterance);
      }
    }
  }, []);

  // ตั้งค่า refs
  useEffect(() => {
    playTTSRef.current = playTTS;
    updateCallStatusRef.current = updateCallStatus;
  }, [playTTS, updateCallStatus]);

  useEffect(() => {
    const fetchSetting = async () => {
      try {
        setLoading(true);
        const result = await fetchWithErrorHandling(`/api/setting/${id}`);
        
        if (result && result.success) {
          console.log('Setting data loaded:', result.data);
          console.log('stem_popup value:', result.data.stem_popup, 'type:', typeof result.data.stem_popup);
          setSetting(result.data);
          // Fetch all data if department_load exists
          if (result.data.department_load) {
            await Promise.all([
              fetchVisitData(result.data.department_load),
              fetchActiveData(result.data.department_load),
              fetchCallData(result.data.department_load),
              fetchSkippedData(result.data.department_load)
            ]);
          }
        } else if (result && !result.success) {
          setError(result.error || 'Failed to fetch setting');
        }
      } catch (err) {
        handleError(err, 'fetchSetting');
      } finally {
        setLoading(false);
      }
    };

    if (id) {
      fetchSetting();
    }
  }, [id, fetchVisitData, fetchActiveData, fetchCallData, fetchSkippedData, retryConnection, handleError, fetchWithErrorHandling]);

  // Use Server-Sent Events for realtime updates
  useEffect(() => {
    if (!setting || !setting.department_load) return;

    const today = new Date().toISOString().split('T')[0];
    const eventSource = new EventSource(
      `/api/data/realtime?department_load=${encodeURIComponent(setting.department_load)}&visit_date=${today}`
    );

    eventSource.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        
        if (data.type === 'update') {
          setVisitData(data.visitData);
          setActiveData(data.activeData);
          
          // Check for call data and skipped data separately
          if (setting?.department_load) {
            fetchCallData(setting.department_load);
            fetchSkippedData(setting.department_load);
          }
        } else if (data.type === 'error') {
          console.error('SSE Error:', data.message);
        }
      } catch (error) {
        console.error('Error parsing SSE data:', error);
      }
    };

    eventSource.onerror = (error) => {
      console.error('SSE Error:', error);
      eventSource.close();
    };

    return () => {
      eventSource.close();
    };
  }, [setting, fetchCallData]);

  // Function to split queue number into letter and number
  const splitQueueNumber = useCallback((queueNo: string | number | null | undefined) => {
    if (!queueNo) return { letter: '-', number: '-' };
    
    const queueStr = String(queueNo);
    const letter = queueStr.replace(/\d+$/, '');
    const number = queueStr.match(/\d+$/)?.[0] || '-';
    
    return { letter, number };
  }, []);

  if (loading) {
    return (
      <div className={styles.container}>
        <div style={{ 
          display: 'flex', 
          justifyContent: 'center', 
          alignItems: 'center', 
          height: '100vh',
          fontSize: '1.2rem',
          color: '#6b7280'
        }}>
          กำลังโหลดข้อมูล...
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div style={{ 
          display: 'flex', 
          justifyContent: 'center', 
          alignItems: 'center', 
          height: '100vh',
          fontSize: '1.2rem',
          color: '#dc2626'
        }}>
          เกิดข้อผิดพลาด: {error}
        </div>
      </div>
    );
  }


  const currentSetting = setting || defaultSetting;

  // Parse station_l to get table names
  const tableNames = currentSetting.station_l ? currentSetting.station_l.split(',') : [];

  return (
    <div className={styles.container}>
      {/* Header */}
      <header className={styles.header}>
        <div className={styles.headerLeft}>
          <div className={styles.hospitalIcon}><Image src="/images/logo/logo.png" alt="hospital" width={32} height={32} /></div>
          <span className={styles.hospitalName}>
            {currentSetting.n_hospital || 'โรงพยาบาลชนบท'}
          </span>
        </div>
        <button className={styles.checkupButton}>
          {currentSetting.department || 'ตรวจโรคทั่วไป'}
        </button>
      </header>

      {/* Main Content */}
      <main className={styles.mainContent}>
        {/* Left Column - Interview Point */}
        <section className={styles.interviewSection}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>
              {currentSetting.n_table || 'จุดซักประวัติ'}
            </h2>
          </div>
          
          <table className={styles.interviewTable}>
            <thead>
              <tr>
                <th className={styles.tableHeader}>
                  <div className={styles.headerItem}>
                    <Hash className={styles.headerIcon} size={20} />
                    <span>หมายเลข</span>
                  </div>
                </th>
                {currentSetting.stem_surname_table !== 'name' && (
                  <th className={styles.tableHeader}>
                    <div className={styles.headerItem}>
                      <User className={styles.headerIcon} size={20} />
                      <span>ชื่อ-นามสกุล</span>
                    </div>
                  </th>
                )}
                {currentSetting.urgent_level === 'true' && (
                  <th className={styles.tableHeader}>
                    <div className={styles.headerItem}>
                      <span>ระดับความเร่งด่วน</span>
                    </div>
                  </th>
                )}
                {currentSetting.status_patient === 'true' && (
                  <th className={styles.tableHeader}>
                    <div className={styles.headerItem}>
                      <span>สถานะ</span>
                    </div>
                  </th>
                )}
              </tr>
            </thead>
            <tbody>
              {visitData.length > 0 ? (
                visitData.map((visit, index) => (
                  <tr key={visit.id || index} className={styles.tableRow}>
                    <td className={styles.tableCell}>
                      <div className={styles.queueNumberContainer}>
                        {currentSetting.urgent_color === 'true' && visit.urgent_color && (
                          <div 
                            className={styles.urgentColorBlock}
                            style={{ backgroundColor: visit.urgent_color }}
                          />
                        )}
                        <span 
                          className={styles.queueNumberLeft}
                          style={{ 
                            color: currentSetting.urgent_color === 'true' && visit.urgent_color
                              ? visit.urgent_color
                              : '#0c266d' 
                          }}
                        >
                          {visit.visit_q_no || ' - '}
                        </span>
                      </div>
                    </td>
                    {currentSetting.stem_surname_table !== 'name' && (
                      <td className={styles.tableCell}>
                        <span 
                          className={styles.patientName}
                          style={{ 
                            color: currentSetting.urgent_color === 'true' && visit.urgent_color
                              ? visit.urgent_color
                              : '#0c266d' 
                          }}
                        >
                          {visit.name || '-'} {currentSetting.stem_surname_table === 'true' ? maskSurname(visit.surname) : (visit.surname || '-')}
                        </span>
                      </td>
                    )}
                    {currentSetting.urgent_level === 'true' && (
                      <td className={styles.tableCell}>
                        <div className={styles.urgentLevel}>
                          {visit.urgent_level ? (
                            <div 
                              className={styles.urgentCircle}
                              style={{ 
                                backgroundColor: visit.urgent_color || '#0066AA'
                              }}
                            />
                          ) : (
                            <div 
                              className={styles.urgentCircle}
                              style={{ 
                                backgroundColor: visit.urgent_color || '#0066AA'
                              }}
                            />
                          )}
                        </div>
                      </td>
                    )}
                    {currentSetting.status_patient === 'true' && (
                      <td className={styles.tableCell}>
                      <span 
                        className={styles.patientName}
                        style={{ 
                          color: currentSetting.urgent_color === 'true' && visit.status_patient as string
                            ? visit.status_patient as string
                            : '#0c266d' 
                        }}
                      >
                        {visit.status_patient || '-'}
                      </span>
                      </td>
                    )}
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={currentSetting.urgent_level === 'true' ? 3 : 2} className={styles.noData}>
                    {currentSetting.n_listtable || 'ไม่มีข้อมูล'}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </section>



        {/* Right Column - Currently Receiving Service */}
        <section className={styles.serviceSection}>
          <h2 className={styles.serviceTitle}>
            กำลังรับบริการ
          </h2>
          
          <div className={styles.serviceCards}>
            {tableNames.length > 0 ? (
              tableNames.map((tableName, index) => {
                // Find active patient data for this station
                const activePatient = activeData.find(visit => 
                  visit.station === tableName.trim() || 
                  visit.station === `โต๊ะ${tableName.trim()}` ||
                  visit.station === `${tableName.trim()}`
                );
                
                return (
                  <div key={index} className={styles.serviceCard}>
                    <div className={styles.serviceInfo}>
                      <span className={styles.serviceText}>
                        {tableName.trim()}
                      </span>
                      {activePatient && currentSetting.stem_surname !== 'name' && (
                        <div className={styles.patientInfo}>
                          <span 
                            className={styles.patientName}
                            style={{ 
                              color: currentSetting.urgent_color === 'true' && activePatient.urgent_color
                                ? activePatient.urgent_color
                                : undefined
                            }}
                          >
                            {activePatient.name || '-'} {currentSetting.stem_surname === 'true' ? maskSurname(activePatient.surname) : (activePatient.surname || '-')}
                          </span>
                        </div>
                      )}
                    </div>
                    <div 
                      className={styles.actionBox}
                      style={{ 
                        backgroundColor: currentSetting.urgent_color === 'true' && activePatient?.urgent_color
                          ? activePatient.urgent_color
                          : '#0066AA'
                      }}
                    >
                      {activePatient ? (
                        <div className={styles.queueNumberSplit}>
                          <span className={styles.queueLetter}>
                            {splitQueueNumber(String(activePatient.visit_q_no || '')).letter}
                          </span>
                          <span className={styles.queueNumber}>
                            {splitQueueNumber(String(activePatient.visit_q_no || '')).number}
                          </span>
                        </div>
                      ) : null}
                    </div>
                  </div>
                );
              })
            ) : (
              // Fallback to default tables if station_l is empty
              Array.from({ length: currentSetting.amount_boxL || 3 }, (_, index) => {
                const stationName = `${currentSetting.n_table || 'โต๊ะซักประวัติ'} ${index + 1}`;
                const activePatient = activeData.find(visit => 
                  visit.station === stationName
                );
                
                return (
                  <div key={index} className={styles.serviceCard}>
                    <div className={styles.serviceInfo}>
                      <span className={styles.serviceText}>
                        {stationName}
                      </span>
                      {activePatient && (
                        <div className={styles.patientInfo}>
                          <span 
                            className={styles.patientName}
                            style={{ 
                              color: currentSetting.urgent_color === 'true' && activePatient.urgent_color
                                ? activePatient.urgent_color
                                : undefined
                            }}
                          >
                            {activePatient.name || '-'} {activePatient.surname || '-'}
                          </span>
                        </div>
                      )}
                    </div>
                    <div 
                      className={styles.actionBox}
                      style={{ 
                        backgroundColor: currentSetting.urgent_color === 'true' && activePatient?.urgent_color
                          ? activePatient.urgent_color
                          : '#0066AA'
                      }}
                    >
                      {activePatient ? (
                        <div className={styles.queueNumberSplit}>
                          <span className={styles.queueLetter}>
                            {splitQueueNumber(String(activePatient.visit_q_no || '')).letter}
                          </span>
                          <span className={styles.queueNumber}>
                            {splitQueueNumber(String(activePatient.visit_q_no || '')).number}
                          </span>
                        </div>
                      ) : null} 
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </section>
      </main>

      {/* Call Popup */}
      {showCallPopup && callData && (
        <div className={styles.callPopup}>
          <div className={styles.callPopupContent}>
            <div className={styles.callHeader}>
              <h3>เรียกคิว</h3>
            </div>
            <div className={styles.callInfo}>
              <div className={styles.callDataLeft}>
                <div className={styles.callTableName}>
                  {callData.station || 'โต๊ะซักประวัติ'}
                </div>
                {currentSetting.stem_popup !== 'name' && (
                  <div className={styles.callPatientName}>
                    {callData.name} {currentSetting.stem_popup === 'true' ? maskSurname(callData.surname) : callData.surname}
                  </div>
                )}
              </div>
              <div className={styles.callQueueBox}>
                <div className={styles.callQueueNumber}>
                  {callData.visit_q_no}
                </div>
              </div>
            </div>

          </div>
        </div>
      )}

      {/* Skipped Queue Bar */}
      {skippedData.length > 0 && (
        <div className={styles.skippedBar}>
          <div className={styles.skippedBarContent}>
            <div className={styles.skippedHeader}>
              <span>รายชื่อที่ข้ามคิว</span>
            </div>
            <div className={styles.skippedQueueContainer}>
              <div className={styles.skippedQueueList}>
                {skippedData.map((item, index) => (
                  <div key={item.id || index} className={styles.skippedQueueItem}>
                    <span className={styles.skippedQueueNumber}>
                      {item.visit_q_no}
                    </span>
                    <span className={styles.skippedQueueName}>
                      {item.name} {item.surname}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
