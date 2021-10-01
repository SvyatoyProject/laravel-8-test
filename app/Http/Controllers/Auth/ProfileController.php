<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

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

        foreach (Storage::files('public/images/users/') as $file) {
            $filename = pathinfo($file, PATHINFO_BASENAME);
            $id = strstr($filename, '_', true);

            if (intval($id) == $user->id)
                $deleteFiles[] = 'public/images/users/'.$filename;
        }

        Storage::put('public/images/users/'.$imageName, $image);
        Storage::delete($deleteFiles);

        User::where('id', $user->id)->update(['image' => 'storage/images/users/'.$imageName]);
    }
}
