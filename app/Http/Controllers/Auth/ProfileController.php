<?php

namespace App\Http\Controllers\Auth;

use File;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController
{
    function GetProfileData(): array
    {
        $user = auth()->user();
        $rights = DB::table('rights')->where('rights', $user->rights)->get();

        return ['rights' => $rights];
    }

    function UploadImage(Request $request)
    {
        $user = auth()->user();
        $base64_str = substr($request->image, strpos($request->image, ",") + 1); // get the image code
        $image = base64_decode($base64_str); // decode the image
        $imageName = $user->id.'_'.time().'.'.$request->type;
        $deleteFiles = [];

        foreach (File::files('images/users/') as $file) {
            $filename = pathinfo($file, PATHINFO_BASENAME);
            $id = strstr($filename, '_', true);

            if (intval($id) == $user->id)
                $deleteFiles[] = 'images/users/'.$filename;
        }

        File::put('images/users/'.$imageName, $image);
        File::delete($deleteFiles);

        User::where('id', $user->id)->update(['image' => 'images/users/'.$imageName]);
    }
}
