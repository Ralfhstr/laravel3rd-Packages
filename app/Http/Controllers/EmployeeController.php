<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;




class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pageTitle = 'Employee List';

        confirmDelete();

        return view('employee.index', compact('pageTitle'));
        // memanggil semua data pada employee
        // $employees = Employee::all();

        // // RAW SQL QUERY
        // $employees = DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name from employees
        //     left join positions on employees.position_id = positions.id
        // ');

        // // Query Builder
        // $employees = DB::table('employees')
        // ->select('*', 'employees.id as employee_id', 'positions.name as position_name')
        // ->leftJoin('positions','employees.position_id', '=', 'positions.id')
        // ->get();

        // return view('employee.index', [
        //     'pageTitle' => $pageTitle,
        //     // 'employees' => $employees
        //  ]);

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';

        // ELOQUENT
        $positions = Position::all();

        // // RAW SQL Query
        // $positions = DB::select('select * from positions');

        // // Query Builder
        // $positions = DB::table('positions')->get();


        return view('employee.create', compact('pageTitle', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        //Eloquent
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

         // // INSERT QUERY
        // DB::table('employees')->insert([
        //     'firstname' => $request->firstName,
        //     'lastname' => $request->lastName,
        //     'email' => $request->email,
        //     'age' => $request->age,
        //     'position_id' => $request->position,
        // ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        // menampilkan data sesuai dengan id yang dipilih
        $employee = Employee::find($id);

        // // RAW SQL QUERY
        // $employee = collect(DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name
        //     from employees
        //     left join positions on employees.position_id = positions.id
        //     where employees.id = ?
        // ', [$id]))->first();

        // // Query Builder
        // $employee = DB::table('employees')
        //             ->select('*', 'employees.id as employee_id', 'positions.name as position_name')
        //             ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        //             ->where('employees.id', '=', $id)
        //             ->first();

        return view('employee.show', compact('pageTitle', 'employee'));

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Employee Edit';

        // menampilkan semua data pada tabel position
        $positions = Position::all();
        // menampilkan data sesuai dengan id yang dipilih
        $employee = Employee::find($id);

        return view('employee.edit', compact('pageTitle', 'positions', 'employee'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

        // update data sesuai id yang dipilih
        $employee = Employee::find($id);
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;

        $file = $request->file('cv');

        if ($file != null) {
            if ($employee->original_filename) {
                Storage::delete('public/files/' . $employee->encrypted_filename);
            }

            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store File
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
        // menghapus data sesuai id yang dipilih
        $employee = Employee::find($id);

        // Menghapus file di storage jika ada
        if ($employee->original_filename) {
            Storage::delete('public/files/' . $employee->encrypted_filename);
        }

        // Menghapus data sesuai id yang dipilih
        $employee->delete();

        Alert::success('Deleted Successfully', 'Employee Data Deleted Successfully.');

        return redirect()->route('employees.index');
    }

    public function exportExcel()
    {
        return Excel::download(new EmployeesExport, 'employees.xlsx');
    }

    public function exportPdf()
    {
        $employees = Employee::all();

        $pdf = PDF::loadView('employee.export_pdf', compact('employees'));

        return $pdf->download('employees.pdf');
    }


}
