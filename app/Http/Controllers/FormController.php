<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use DateTime;
use App\User;
use App\Form;
use App\FormAuth;

use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendAuthorizeMail;

use Illuminate\Http\Request;
date_default_timezone_set('Asia/Singapore');

class FormController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
    public function __construct()
    {
        $this->middleware(['auth' => 'verified']);
    }

    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();

        $form = Form::where('id', $request->id)
                  ->get();
        $form = DB::table('forms')
                      ->join('formauths', 'formauths.form_id', '=', 'forms.id')
                      ->leftJoin('users', 'users.email', '=', 'formauths.email')
                      ->where('forms.id', $request->id)
                      ->select('forms.*', 'formauths.email', 'formauths.status', 'formauths.reason', 'formauths.updated_at as authdate','users.name')
                      ->first();


        $docs = array(); // attachments
        if($form)
          if($form->path)
          {
              $dir_tmp = public_path('docs/' . $form->path);
              if(file_exists($dir_tmp)){
                $files = scandir($dir_tmp, 1);

                foreach(File::allFiles($dir_tmp) as $file)
                {
                    $d = [];
                    $ext = \File::extension($file);

                    if ($ext != '')
                    {
                      $d = [
                        'path' => 'docs/' .$form->path .'/' .$file->getFilename(),
                        'name' => $file->getFilename(),
                        'size' => $this->bytesToHuman($file->getSize()),
                      ];

                      array_push($docs, $d);
                    }
                }
              }
        }

        $out = [
          'form' => $form,
          'user_id' =>  $user_id,
          'user_name' => $user->name,
          'docs' => $docs
        ];

        //return $out;
        return view('form', $out);
    }

    public function GetForms()
    {
        //
        $user_id = Auth::user()->id;
        $form = DB::table('forms')
                      ->join('formauths', 'formauths.form_id', '=', 'forms.id')
                      ->leftJoin('users', 'users.email', '=', 'formauths.email')
                      ->select('forms.*', 'formauths.email', 'formauths.status', 'formauths.reason', 'formauths.updated_at as authdate','users.name')
                      ->get();

        return ($form);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function show(Form $form)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function edit(Form $form)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $id = $request->doc_id;
        $form = Form::where('id', $id);

        if ($form->first())
        {
          // update
          $form->update([
            'description' =>  $request->desc,
            'category' =>  $request->category,
            'duedate' =>  new DateTime($request->duedate),
            'updatedby' => $request->user_id,
            'updated_at' => new DateTime(),

          ]);

        }
        else
        {
            // insert new record
            $form = new Form;
            $form->title = $request->title;
            $form->description = $request->description;
            $form->duedate = new DateTime($request->duedate);
            $form->path = $request->path;
            $form->createdby = $request->user_id;
            $form->updatedby = $request->user_id;

        }
        $form->save();
        $request->url = strval(public_path(). "\\". $form->id);

        // form authorize
        $formAuth = FormAuth::where('form_id', $form->id);

        if ($formAuth->first())
        {
          // update
          $formAuth -> update([
            'email' =>  $request->email,
            'status' =>  $request->status,
            'reason' =>  $request->reason,
            'updatedby' => $request->user_id,
            'updated_at' => new DateTime(),

          ]);

        }
        else
        {
            // insert new record
            $formAuth = new FormAuth;
            $formAuth->form_id = $form->id; // form id
            $formAuth->email = $request->email;
            $formAuth->status = $request->status;
            $formAuth->reason = $request->reason;
            $formAuth->createdby = $request->user_id;
            $formAuth->updatedby = $request->user_id;

        }
        $formAuth->save();

        if($request->status == 'Pending')
        {
            //SendMail
            $data = request()->validate([
              'name' => 'required',
              'email' => 'required|email',
              'message' => 'required',
            ]);

            Mail::to($request->email)->send(new SendAuthorizeMail($data, request()->getHttpHost(). '/form/'. $form->id));
        }

        return '';
    }

    public function upload(Request $request)
    {
        //
        $file = $request->file('file');
        $folderName = strval($request->folder);
        $profileDoc = $file->getClientOriginalName();

        $destinationPath = public_path('/docs/'. $folderName. '/'); // upload path
        $file->move($destinationPath,$profileDoc);

        return response()->json([
          'filename' => $profileDoc,
          'path' => '\\docs\\'. $folderName. '\\'. $profileDoc
        ]);
        /*
        $image = $request->file('file');
        $profileImage = $image->getClientOriginalName();

        // Define upload path
        $destinationPath = public_path('/profile_images/'); // upload path
        $image->move($destinationPath,$profileImage);

        // Save In Database
        $imagemodel= new Photo();
        $imagemodel->photo_name="$profileImage";
        $imagemodel->save();

        return response()->json(['success'=>$profileImage]);
        */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form)
    {
        //
    }
}
