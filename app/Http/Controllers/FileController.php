<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFavoritesRequest;
use App\Http\Requests\FileActionRequest;
use App\Http\Requests\ShareFilesRequest;
use App\Http\Requests\StoreFilerequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\TrashFilesRequest;
use App\Http\Resources\FileResource;
use App\Mail\ShareFilesMail;
use App\Models\File;
use App\Models\File_Share;
use App\Models\FileShare;
use App\Models\Starred_file;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $folder = null)
    {

        $search = $request->get('search');
        if ($folder) {
            $folder = File::query()
                ->where('created_by', Auth::id())
                ->where('path', $folder)
                ->firstOrFail();
        }

        if (!$folder) {
            $folder = $this->getRoot();
        }

        $favourites = (int)$request->get('favourites');

        $filesQuery = File::query()
            ->select('files.*')
            ->with('starred')
            ->where('created_by', Auth::id())
            ->where('_lft', '!=', 1)
            ->orderBy('is_folder', 'desc')
            ->orderBy('files.created_at', 'desc')
            ->orderBy('files.id', 'desc');

        if ($search) {
            $filesQuery->where('name', 'like', "%$search%");
        } else {
            $filesQuery->where('parent_id', $folder->id);
        }

        if ($favourites === 1) {
            $filesQuery->join('starred_files', 'starred_files.file_id', '=', 'files.id')
                ->where('starred_files.user_id', Auth::id());
        }
        $files = $filesQuery->paginate(10);

        $files = FileResource::collection($files);
        if ($request->wantsJson()) {
            return $files;
        }


        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        $folders = new FileResource($folder);

        return Inertia::render('Dashboard/MyFiles', [
            'files' => $files,
            'folders' => $folders,
            'ancestors' => $ancestors,
        ]);
    }

    public function trash(Request $request)
    {
        $search = $request->get('search');
        $query = File::onlyTrashed()
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('deleted_at', 'desc');

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $files = $query->paginate(10);

        $files = FileResource::collection($files);
        if ($request->wantsJson()) {
            return $files;
        }
        return Inertia::render('Dashboard/Trash', compact('files'));
    }

    /**
     * Show the form for private a new resource.
     */

    private function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }

    /**
     * create a newly created resource in storage.
     */
    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];

        // Append the new folder to the parent node
        $parent->appendNode($file);
    }

    /**
     * Display the specified resource.
     */
    public function store(StoreFilerequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        $user = $request->user();
        $fileTree = $request->file_tree;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        if (!empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                /** @var \Illuminate\Http\UploadedFile $file */
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    public function saveFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {

                $this->saveFile($file, $user, $parent);
            }
        }
    }

    private function saveFile($file, $user, $parent): void
    {
        $path = $file->store('/files/' . $user->id);

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        $parent->appendNode($model);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;


        if ($data['all']) {
            $children = $parent->children;

            foreach ($children as $child) {
                $child->moveToTrash();
            }
        } else {
            foreach ($data['ids'] ?? [] as $id) {
                $file = File::find($id);
                if ($file) {
                    $file->moveToTrash();
                }
            }
        }
        return to_route('myFiles', ['folder' => $parent->path]);
    }

    public function download(FileActionRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }
        if ($all) {
            $url = $this->createZip($parent->children);
            $fileName = $parent->name . '.zip';
        } else {
            if (count($ids) === 1) {
                $file = File::find($ids[0]);
                if ($file->is_folder) {
                    if ($file->children->count() === 0) {
                        return [
                            'message' => 'The Folder is empty'
                        ];
                    }
                    $url = $this->createZip($file->children);
                    $fileName = $file->name . '.zip';
                } else {
                    $dest = 'public/' . pathinfo($file->storage_path, PATHINFO_BASENAME);
                    Storage::copy($file->storage_path, $dest);

                    $url = asset(Storage::url($dest));
                    $fileName = $file->name;
                }
            } else {
                $files = File::query()->whereIn('id', $ids)->get();
                $url = $this->createZip($files);

                $fileName = $parent->name . '.zip';
            }
        }
        return [
            'url' => $url,
            'fileName' => $fileName
        ];
    }




    public function createZip($files)
    {
        $zipPath = 'zip/' . Str::random() . '.zip';
        $publicPath = "public/$zipPath";

        if (!is_dir(dirname($publicPath))) {
            Storage::makeDirectory(dirname($publicPath));
        }
        $zipFile = Storage::path($publicPath);
        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }
        $zip->close();
        return asset(Storage::url($zipPath));
    }

    private function addFilesToZip($zip, $files, $ancestors = '')
    {
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancestors . $file->name . '/');
            } else {
                $zip->addFile(Storage::path($file->storage_path), $ancestors . $file->name);
            }
        }
    }

    public function restore(TrashFilesRequest $request)
    {
        $data = $request->validated();
        if ($data['all']) {
            $children = FIle::onlyTrashed()->get();
            foreach ($children as $child) {
                $child->restore();
            }
        } else {
            $ids = $data['ids'] ?? [];
            $children = File::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($children as $child) {
                $child->restore();
            }
        }
        return to_route('trash');
    }

    public function deleteForever(TrashFilesRequest $request)
    {
        $data = $request->validated();
        if ($data['all']) {
            $children = FIle::onlyTrashed()->get();
            foreach ($children as $child) {
                $child->deleteForever();
            }
        } else {
            $ids = $data['ids'] ?? [];
            $children = File::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($children as $child) {
                $child->deleteForever();
            }
        }
        return to_route('trash');
    }

    public function addFavorites(AddFavoritesRequest $request)
    {
        $data = $request->validated();

        $id = $data['id'];
        $user_id = Auth::id();
        $file = File::findOrFail($id);

        $starredFile = Starred_file::query()
            ->where('user_id', $user_id)
            ->where('file_id', $file->id)
            ->first();



        if ($starredFile) {
            $starredFile->delete();
        } else {
            Starred_file::create([
                'user_id' => $user_id,
                'file_id' => $file->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return redirect()->back();
    }

    public function share(ShareFilesRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $email = $data['email'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please Select files to share'
            ];
        }
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            return redirect()->back();
        }
        if ($all) {
            $files = $parent->children;
        } else {
            $files = File::find($ids);
        }

        $data = [];
        $ids = Arr::pluck($files, 'id');

        $existingFileIds = FileShare::query()
            ->whereIn('file_id', $ids)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('file_id');

        foreach ($files as $file) {
            if ($existingFileIds->has($file->id)) {
                continue;
            }
            $data[] = [
                'user_id' => $user->id,
                'file_id' => $file->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        FileShare::insert($data);
        // Todo send email request
        Mail::to($user)->send(new ShareFilesMail($user, Auth::user(), $files));


        return redirect()->back();
    }

    public function shareWithMe(Request $request)
    {
        $search = $request->get('search');
        $query = File::getShareWithMe();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }
        $files = $query->paginate(10);


        $files = FileResource::collection($files);
        if ($request->wantsJson()) {
            return $files;
        }
        return Inertia::render('Dashboard/ShareWithMe', compact('files'));
    }
    public function shareByMe(Request $request)
    {
        $search = $request->get('search');
        $query = File::getShareByMe();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $files = $query->paginate(10);

        $files = FileResource::collection($files);
        if ($request->wantsJson()) {
            return $files;
        }
        return Inertia::render('Dashboard/ShareByMe', compact('files'));
    }

    public function downloadShareWithMe(FileActionRequest $request)
    {
        $data = $request->validated();
        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }
        $zipName = 'share_with_me';
        if ($all) {
            $files = File::getShareWithMe()->get();
            $url = $this->createZip($files);
            $fileName = $zipName . '.zip';
        } else {
            [$url, $fileName] = $this->getDownloadUrl($ids, $zipName);
        }
        return [
            'url' => $url,
            'fileName' => $fileName
        ];
    }

    public function downloadShareByMe(FileActionRequest $request)
    {
        $data = $request->validated();
        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }
        $zipName = 'share_by_me';
        if ($all) {
            $files = File::getShareByMe()->get();
            $url = $this->createZip($files);
            $fileName = $zipName . '.zip';
        } else {
            [$url, $fileName] = $this->getDownloadUrl($ids, $zipName);
        }
        return [
            'url' => $url,
            'fileName' => $fileName
        ];
    }

    private function getDownloadUrl(array $ids, $zipName)
    {
        if (count($ids) === 1) {
            $file = File::find($ids[0]);
            if ($file->is_folder) {
                if ($file->children->count() === 0) {
                    return [
                        'message' => 'The Folder is empty'
                    ];
                }
                $url = $this->createZip($file->children);
                $fileName = $file->name . '.zip';
            } else {
                $dest = 'public/' . pathinfo($file->storage_path, PATHINFO_BASENAME);
                Storage::copy($file->storage_path, $dest);

                $url = asset(Storage::url($dest));
                $fileName = $file->name;
            }
        } else {
            $files = File::query()->whereIn('id', $ids)->get();
            $url = $this->createZip($files);

            $fileName = $zipName . '.zip';
        }
        return [$url, $fileName];
    }
}
