<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\Position;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use PDF;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function exportPdf()
{
    $employees = Employee::all();

    $pdf = PDF::loadView('employee.export_pdf', compact('employees'));

    return $pdf->download('employees.pdf');
}
     public function exportExcel()
    {
    return Excel::download(new EmployeesExport, 'employees.xlsx');
    }

    public function index()
{
     $pageTitle = 'Employee List';
     confirmDelete();
     $positions = Position::all();
     return view('employee.index',[
            'pageTitle' => $pageTitle, 
            'positions' => $positions
     ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';
    
        // ELOQUENT
        $positions = Position::all();
    
        return view('employee.create', compact('pageTitle', 'positions'));
    }
    
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];
    
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Get File
        $file = $request->file('cv');
    
        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();
    
            // Store File
            $file->store('public/files');
        }
    
        // ELOQUENT
        $employee = New Employee;
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;
    
        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }
    
        $employee->save();

        Alert::success('Added Successfully', 'Employee Data Added Successfully.');

        return redirect()->route('employees.index');

    }
    /**
     * Display the specified resource.
     */

     public function show(string $id)
     {
         $pageTitle = 'Employee Detail';
     
         // ELOQUENT
         $employee = Employee::find($id);
     
         return view('employee.show', compact('pageTitle', 'employee'));
     }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
{
    $pageTitle = 'Edit Employee';

    // ELOQUENT
    $positions = Position::all();
    $employee = Employee::find($id);

    return view('employee.edit', compact('pageTitle', 'positions', 'employee'));
}

    public function update(Request $request, string $id)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar.',
            'numeric' => 'Isi :attribute dengan angka.'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ELOQUENT
        $employee = Employee::find($id);
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;

        // Proses File CV Baru
        $file = $request->file('cv');

        if ($file) {
            // Hapus file CV lama jika ada
            if ($employee->encrypted_filename) {
                $oldFile = 'public/files/' . $employee->encrypted_filename;
                if (Storage::exists($oldFile)) {
                    Storage::delete($oldFile);
                }
            }

            // Simpan file CV baru
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            $file->store('public/files');

            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        Alert::success('Changed Successfully', 'Employee Data Changed Successfully.');
    
        return redirect()->route('employees.index');
    }
    /**
     * Remove the specified resource from storage.
     */
        public function destroy(string $id)
    {
        // Temukan employee berdasarkan ID
        $employee = Employee::findOrFail($id);

        // Hapus file CV jika ada
        if ($employee->encrypted_filename) {
            $filePath = 'public/files/' . $employee->encrypted_filename;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }

        // Hapus data employee dari database
        $employee->delete();

        Alert::success('Deleted Successfully', 'Employee Data Deleted Successfully.');

        return redirect()->route('employees.index');

    }

    public function downloadFile($employeeId)
    {
        $employee = Employee::find($employeeId);
        $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
        $downloadFilename = Str::lower($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

        if(Storage::exists($encryptedFilename)) {
            return Storage::download($encryptedFilename, $downloadFilename);
        }
    }

    public function getData(Request $request)
{
    $employees = Employee::with('position');

    if ($request->ajax()) {
        return datatables()->of($employees)
            ->addIndexColumn()
            ->addColumn('actions', function($employee) {
                return view('employee.actions', compact('employee'));
            })
            ->toJson();
    }
}
}
