<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use DateTime;
use App\User;
use App\Form;
use App\FormAuth;

use File;
use Illuminate\Http\Request;

class FormAuthController extends Controller
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
    public function formauth($id = null)
    {
        //
        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->first();

        $form = DB::table('forms')
                      ->join('formauths', 'formauths.form_id', '=', 'forms.id')
                      ->leftJoin('users', 'users.email', '=', 'formauths.email')
                      ->where('forms.id', $id)
                      ->where('formauths.email', $user->email) // make sure correct authorize email addres
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
                      'path' => '../docs/' .$form->path .'/' .$file->getFilename(),
                      'name' => $file->getFilename(),
                      'size' => $this->bytesToHuman($file->getSize()),
                    ];

                    array_push($docs, $d);
                  }
              }
            }
          }

        $out = [
          'user_id' => $user_id,
          'user_name' => $user->name,
          'form' => $form,
          'docs' => $docs
        ];

        //return $out;
        return view('formauth', $out);
    }

    public function index()
    {

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
     * @param  \App\FormAuth  $formAuth
     * @return \Illuminate\Http\Response
     */
    public function show(FormAuth $formAuth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FormAuth  $formAuth
     * @return \Illuminate\Http\Response
     */
    public function edit(FormAuth $formAuth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FormAuth  $formAuth
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormAuth $formAuth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FormAuth  $formAuth
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormAuth $formAuth)
    {
        //
    }
}
