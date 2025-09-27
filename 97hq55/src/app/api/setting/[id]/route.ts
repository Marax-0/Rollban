import { NextRequest, NextResponse } from 'next/server';
import sql from 'mssql';
import { getConnection } from '@/lib/db-config';

export async function GET(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    
    // Validate id parameter
    if (!id || isNaN(Number(id))) {
      return NextResponse.json(
        { error: 'Invalid ID parameter' },
        { status: 400 }
      );
    }

    // Get connection from pool
    const connection = await getConnection();
    
    // Execute query
    const result = await connection.request()
      .input('id', sql.Int, parseInt(id))
      .query('SELECT * FROM setting WHERE id = @id');

    // Check if data exists
    if (result.recordset.length === 0) {
      return NextResponse.json(
        { error: 'Setting not found' },
        { status: 404 }
      );
    }

    // Return the setting data
    return NextResponse.json({
      success: true,
      data: result.recordset[0]
    });

  } catch (error) {
    console.error('Database error:', error);
    
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    
    return NextResponse.json(
      { 
        error: 'Internal server error',
        details: process.env.NODE_ENV === 'development' ? errorMessage : undefined
      },
      { status: 500 }
    );
  }
}

export async function PUT(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    const body = await request.json();
    
    // Validate id parameter
    if (!id || isNaN(Number(id))) {
      return NextResponse.json(
        { error: 'Invalid ID parameter' },
        { status: 400 }
      );
    }

    // Get connection from pool
    const connection = await getConnection();
    
    // Get current data to check if exists
    const checkResult = await connection.request()
      .input('id', sql.Int, parseInt(id))
      .query('SELECT id FROM setting WHERE id = @id');
    
    if (checkResult.recordset.length === 0) {
      return NextResponse.json(
        { error: 'Setting not found' },
        { status: 404 }
      );
    }

    // Build dynamic UPDATE query based on provided fields
    const updateFields = Object.keys(body).filter(key => key !== 'id');
    if (updateFields.length === 0) {
      return NextResponse.json(
        { error: 'No fields to update' },
        { status: 400 }
      );
    }

    // Validate each field value before processing
    for (const field of updateFields) {
      const value = body[field];
      if (value === undefined || value === null) {
        return NextResponse.json(
          { error: `Field '${field}' cannot be null or undefined` },
          { status: 400 }
        );
      }
    }

    const setClause = updateFields.map(field => `${field} = @${field}`).join(', ');
    const query = `UPDATE setting SET ${setClause} WHERE id = @id`;
    
    // Build request with all parameters
    const updateRequest = connection.request().input('id', sql.Int, parseInt(id));
    updateFields.forEach(field => {
      const value = body[field];
      
      try {
        // Determine appropriate SQL type based on field name and value
        if (field === 'id' || field.includes('id') || field.includes('Id')) {
          const intValue = parseInt(String(value));
          if (isNaN(intValue)) {
            throw new Error(`Invalid integer value for field '${field}': ${value}`);
          }
          updateRequest.input(field, sql.Int, intValue);
        } else if (field.includes('date') || field.includes('Date')) {
          updateRequest.input(field, sql.Date, value);
        } else if (field.includes('time') || field.includes('Time')) {
          updateRequest.input(field, sql.DateTime, value);
        } else if (typeof value === 'number') {
          updateRequest.input(field, sql.Int, value);
        } else if (typeof value === 'boolean') {
          updateRequest.input(field, sql.Bit, value);
        } else {
          // Default to VarChar for string values
          updateRequest.input(field, sql.VarChar, String(value));
        }
      } catch (fieldError) {
        console.error(`Error processing field '${field}':`, fieldError);
        throw new Error(`Invalid data type for field '${field}': ${fieldError instanceof Error ? fieldError.message : 'Unknown error'}`);
      }
    });
    
    await updateRequest.query(query);
    
    // Get updated data
    const result = await connection.request()
      .input('id', sql.Int, parseInt(id))
      .query('SELECT * FROM setting WHERE id = @id');

    return NextResponse.json({
      success: true,
      message: 'Setting updated successfully',
      data: result.recordset[0]
    });

  } catch (error) {
    console.error('Database error:', error);
    
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    
    return NextResponse.json(
      { 
        error: 'Internal server error',
        details: process.env.NODE_ENV === 'development' ? errorMessage : undefined
      },
      { status: 500 }
    );
  }
}

export async function DELETE(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    
    // Validate id parameter
    if (!id || isNaN(Number(id))) {
      return NextResponse.json(
        { error: 'Invalid ID parameter' },
        { status: 400 }
      );
    }

    // Get connection from pool
    const connection = await getConnection();
    
    // Check if setting exists
    const checkResult = await connection.request()
      .input('id', sql.Int, parseInt(id))
      .query('SELECT id FROM setting WHERE id = @id');
    
    if (checkResult.recordset.length === 0) {
      return NextResponse.json(
        { error: 'Setting not found' },
        { status: 404 }
      );
    }

    // Delete the setting
    await connection.request()
      .input('id', sql.Int, parseInt(id))
      .query('DELETE FROM setting WHERE id = @id');

    return NextResponse.json({
      success: true,
      message: 'Setting deleted successfully'
    });

  } catch (error) {
    console.error('Database error:', error);
    
    const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
    
    return NextResponse.json(
      { 
        error: 'Internal server error',
        details: process.env.NODE_ENV === 'development' ? errorMessage : undefined
      },
      { status: 500 }
    );
  }
}
