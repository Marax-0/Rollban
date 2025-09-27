/**
 * Google Text-to-Speech API Integration
 * ใช้สำหรับแปลงข้อความเป็นเสียงพูด
 */

export interface TTSOptions {
  text: string;
  language?: string;
  voice?: string;
  speed?: number;
  pitch?: number;
}

export interface GoogleTTSResponse {
  audioContent: string;
  audioConfig: {
    audioEncoding: string;
    speakingRate: number;
    pitch: number;
  };
}

/**
 * ฟังก์ชันสำหรับเรียกใช้ Google TTS API
 */
export async function synthesizeText(options: TTSOptions): Promise<string | null> {
  try {
    const apiKey = process.env.NEXT_PUBLIC_GOOGLE_TTS_API_KEY;
    
    if (!apiKey) {
      console.error('Google TTS API key not found');
      return null;
    }

    const requestBody = {
      input: { text: options.text },
      voice: {
        languageCode: options.language || 'th-TH',
        name: options.voice || 'th-TH-Standard-A', // เสียงผู้ชายไทย
        ssmlGender: 'NEUTRAL'
      },
      audioConfig: {
        audioEncoding: 'MP3',
        speakingRate: options.speed || 0.6,
        pitch: options.pitch || 1.0,
        volumeGainDb: 0.0
      }
    };

    const response = await fetch(
      `https://texttospeech.googleapis.com/v1/text:synthesize?key=${apiKey}`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestBody),
      }
    );

    if (!response.ok) {
      const errorData = await response.json();
      console.error('Google TTS API error:', errorData);
      return null;
    }

    const data: GoogleTTSResponse = await response.json();
    return data.audioContent;
  } catch (error) {
    console.error('Error calling Google TTS API:', error);
    return null;
  }
}

/**
 * ฟังก์ชันสำหรับเล่นเสียงจาก base64 audio data
 */
export function playAudioFromBase64(base64Audio: string): Promise<void> {
  return new Promise((resolve, reject) => {
    try {
      // แปลง base64 เป็น blob
      const audioData = atob(base64Audio);
      const audioArray = new Uint8Array(audioData.length);
      for (let i = 0; i < audioData.length; i++) {
        audioArray[i] = audioData.charCodeAt(i);
      }
      
      const audioBlob = new Blob([audioArray], { type: 'audio/mp3' });
      const audioUrl = URL.createObjectURL(audioBlob);
      
      const audio = new Audio(audioUrl);
      
      audio.onended = () => {
        URL.revokeObjectURL(audioUrl);
        resolve();
      };
      
      audio.onerror = (error) => {
        URL.revokeObjectURL(audioUrl);
        reject(error);
      };
      
      audio.play();
    } catch (error) {
      reject(error);
    }
  });
}

/**
 * ฟังก์ชันหลักสำหรับเล่นเสียง TTS
 */
export async function playGoogleTTS(options: TTSOptions): Promise<void> {
  try {
    const audioBase64 = await synthesizeText(options);
    
    if (audioBase64) {
      await playAudioFromBase64(audioBase64);
    } else {
      console.error('Failed to synthesize text');
      // Fallback ไปใช้ browser TTS
      playBrowserTTS(options);
    }
  } catch (error) {
    console.error('Error playing Google TTS:', error);
    // Fallback ไปใช้ browser TTS
    playBrowserTTS(options);
  }
}

/**
 * Fallback function ใช้ browser TTS
 */
export function playBrowserTTS(options: TTSOptions): void {
  if ('speechSynthesis' in window) {
    const utterance = new SpeechSynthesisUtterance(options.text);
    utterance.lang = options.language || 'th-TH';
    utterance.rate = options.speed || 0.6;
    utterance.pitch = options.pitch || 1.0;
    speechSynthesis.speak(utterance);
  }
}

/**
 * รายการเสียง Google TTS ที่รองรับภาษาไทย
 */
export const THAI_VOICES = {
  STANDARD_A: 'th-TH-Standard-A', // เสียงผู้ชาย
  STANDARD_B: 'th-TH-Standard-B', // เสียงผู้หญิง
  STANDARD_C: 'th-TH-Standard-C', // เสียงผู้ชาย (สำเนียงต่าง)
  STANDARD_D: 'th-TH-Standard-D', // เสียงผู้หญิง (สำเนียงต่าง)
  NEURAL_A: 'th-TH-Neural2-A',    // เสียง Neural ผู้ชาย
  NEURAL_B: 'th-TH-Neural2-B',    // เสียง Neural ผู้หญิง
  NEURAL_C: 'th-TH-Neural2-C',    // เสียง Neural ผู้ชาย (สำเนียงต่าง)
  NEURAL_D: 'th-TH-Neural2-D'     // เสียง Neural ผู้หญิง (สำเนียงต่าง)
} as const;
