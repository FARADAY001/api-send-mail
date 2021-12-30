<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyApiMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function sendEmail(Request $request){
        $data = [
            'title'     => $request->title,
            'receiver'  => $request->receiver,
            'subject'   => $request->subject,
            'message'   => $request->message,
        ];

        $validator = Validator::make($data,[
            'title'      => 'required|string|min:3|max:125',
            'receiver'   => 'required|array|min:1',
            //'receiver'   => 'required|string|min:5',
            'receiver.*' => 'email|min:5',
            'subject'    => 'required|string|min:3|max:125',
            'message'    => 'required|string|max:400'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                404
            );
        }
        $details = [
            'title'     => $request->title,
            'subject'   => $request->subject,
            'message'   => $request->message,
        ];

        foreach($request->receiver as $receiver){
            Mail::to($receiver)->queue(new MyApiMail($details));
        }
    
        return response()->json(['message' => 'Email envoyé avec succès']);
    }
}
