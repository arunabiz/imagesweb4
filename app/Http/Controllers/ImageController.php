<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Google\Client;
// use Google\Service\Drive;
// use App\Models\Image;

// class ImageController extends Controller
// {
//     public function uploadImage(Request $request)
//     {
//         $validatedData = $request->validate([
//             'name' => 'required|string|max:255',
//             'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//         ]);

//         $image = $request->file('image');
//         $name = $validatedData['name'];
//         $extension = $image->getClientOriginalExtension();
//         $tempName = uniqid().'.'.$extension;
//         $tempFilePath = public_path('/temp-images/' . $tempName);

//         $image->move(public_path('/temp-images/'), $tempName);

//         $imageModel = new Image;
//         $imageModel->name = $name;
//         $imageModel->path = $tempFilePath;
//         $imageModel->save();

//         $client = new Client();
//         $client->setAuthConfig('client_secret_821524286801-p8rnr7bcl8dsinpl7cjct1btb7moapk5.apps.googleusercontent.com.json');
//         $client->setScopes([Drive::DRIVE_FILE]);
//         $drive = new Drive($client);
//         $folderName = "Uploaded Images";
//         $folderMimeType = 'application/vnd.google-apps.folder';
//         $fileMetadata = new \Google_Service_Drive_DriveFile(array(
//             'name' => $folderName,
//             'mimeType' => $folderMimeType,
//             'parents' => array(),
//         ));
//         $folder = $drive->files->create($fileMetadata, array(
//             'fields' => 'id'
//         ));
//         $folderId = $folder->getId();
//         $fileMetadata = new \Google_Service_Drive_DriveFile(array(
//             'name' => $name,
//             'parents' => array($folderId),
//         ));
//         $content = file_get_contents($tempFilePath);
//         $file = $drive->files->create($fileMetadata, array(
//             'data' => $content,
//             'mimeType' => 'image/jpeg',
//             'uploadType' => 'multipart',
//             'fields' => 'id'
//         ));
//         $imageModel->google_drive_file_id = $file->id;
//         $imageModel->save();

//         return view('image.uploaded')->with('tempFilePath', $tempFilePath)->with('name', $name);
//     }
//}

use App\Models\Image;
use Google\Client;
use Google\Service\Drive;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('image');
        $name = $validatedData['name'];
        $extension = $image->getClientOriginalExtension();
        $tempName = uniqid().'.'.$extension;
        $tempFilePath = public_path('/temp-images/' . $tempName);

        $image->move(public_path('/temp-images/'), $tempName);

        $client = new Client();
        $client->setAuthConfig(storage_path('app/client_secret_821524286801-p8rnr7bcl8dsinpl7cjct1btb7moapk5.apps.googleusercontent.com.json'));
        $client->setApplicationName('Your App Name');
        $client->setScopes(Drive::DRIVE_FILE);
        $service = new Drive($client);

        $folderId = '1L47PbKZkXL5OlOrDNvVY0FIc1ZROcg6_'; // Replace with the ID of the folder in Google Drive where you want to store the images

        $fileMetadata = new Drive\File();
        $fileMetadata->setName($name);
        $fileMetadata->setParents(array($folderId));
        $file = new Drive\File();
        $file->setMimeType($image->getClientMimeType());
        $content = file_get_contents($tempFilePath);
        $file = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $image->getClientMimeType(),
            'uploadType' => 'multipart'
        ));

        $imageModel = new Image;
        $imageModel->name = $name;
        $imageModel->path = $file->getWebViewLink();
        $imageModel->save();

        unlink($tempFilePath); // Delete the temporary file

        return view('image.uploaded')->with('tempFilePath', $file->getWebViewLink())->with('name', $name);
    }
}
