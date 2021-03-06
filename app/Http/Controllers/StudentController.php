<?php

namespace App\Http\Controllers;

use App\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function view(Request $request)
    {
        $data   = [];
        $limit  = $request->length;
        $start  = $request->start;
        $search = $request->search['value'];

        $recordTotal     = Student::count();
        $recordsFiltered = $recordTotal;

        if(empty($search)) 
        {
            $students = Student::offset($start)->limit($limit)->get();
        }
        else
        {
            $students = Student::where('name', 'LIKE', "%$search%")
                        ->orWhere('roll', 'LIKE', "%$search%")
                        ->orWhere('gender', 'LIKE', "%$search%")
                        ->orWhere('religion', 'LIKE', "%$search%")
                        ->offset($start)
                        ->limit($limit)
                        ->get();

            $recordsFiltered = Student::where('name','LIKE',"%$search%")
                                ->orWhere('roll', 'LIKE', "%$search%")
                                ->orWhere('gender', 'LIKE', "%$search%")
                                ->orWhere('religion', 'LIKE', "%$search%")
                                ->count();
        }

        foreach ($students as $student) 
        {
            $row   = [];
            $row[] = $student->name;
            $row[] = $student->roll;
            $row[] = $student->gender;
            $row[] = $student->religion;
            $row[] = $student->date;
            $row[] = '
            <a class="btn btn-info btn-xs editStudent" data-id="' . $student->student_id . '"><i class="glyphicon glyphicon-edit"></i></a>
            <a class="btn btn-danger btn-xs deleteStudent" data-id="' . $student->student_id . '"><i class="glyphicon glyphicon-trash"></i></a>';
            $data[] = $row;
        }
        
        $output = [
            "draw"            => $request->input('draw'),
            "recordsTotal"    => $recordTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data,
        ];
        echo json_encode($output);
    }

    public function store(Request $request)
    {
        $s           = new Student();
        $s->name     = $request['name'];
        $s->roll     = $request->roll;
        $s->gender   = $request->gender;
        $s->religion = $request->religion;
        $s->date     = Carbon::now('Asia/Dhaka');
        $s->save();
        
        if ($s->save()) 
        {
            $message = 'Data has saved successfully.';
            echo json_encode($message);
        }
    }

    public function edit(){
        $student = Student::find(request()->student_id);
        echo json_encode($student);
    }

    public function update(Request $request)
    {
        $s           = Student::find($request->student_id);
        $s->name     = $request->name;
        $s->roll     = $request->roll;
        $s->gender   = $request->gender;
        $s->religion = $request->religion;
        $s->date     = Carbon::now('Asia/Dhaka');
        $s->save();
        
        if ($s->save()) 
        {
            $message = 'Data has updated successfully.';
            echo json_encode($message);
        }
    }

    public function delete()
    {
        $s = Student::find(request()->student_id);
        if($s->delete()){
            $message = 'Data has deleted';
            echo json_encode($message);
        }

    }

    public function view_bar_chart()
    {
        $religion = Student::selectRaw('count(*) AS cnt, religion')->groupBy('religion')->orderBy('date', 'asc')->get();
        return view('chart',compact('religion'));
    }

}
