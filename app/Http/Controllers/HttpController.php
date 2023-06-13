<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use stdClass;

class HttpController extends Controller
{
    public function index()
    {
        $students = Student::get();

        return response()->json([
            'data' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $data = [];

        try {
            Student::create([
                'name' => $request->name,
                'age' => $request->age,
                'gender' => $request->gender,
                'address' => $request->address
            ]);
    
            $msg = 'success';

        } catch (\Throwable $th) {

           $msg = 'Error';

        }

        $data['msg'] = $msg;

        return response()->json([
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        $student = Student::where('id', $id)->firstOrFail();

        $data = [
            'student' => $student,
        ];

        return response()->json([
            'data' => $data,
        ]);
    }

    public function update(Request $request)
    {
        Student::where('id', $request->id)->update([
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'address' => $request->address
        ]);

        return 'success';
    }

    public function delete($id)
    {
        Student::find($id)->delete();

        return 'success';
    }
}
