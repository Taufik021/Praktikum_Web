<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Media::with('users');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        $media = $query->get();

        return MediaResource::collection($media);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request)
    {
        //
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional([
                'message' => 'Media berhasil ditambahkan',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $media)
    {
        //
        $media = Media::with('users')->findOrFail($media);

        return (new MediaResource($media))
            ->additional([
                'message' => 'Detail media',
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, string $media)
    {
        //
        $media = Media::findOrFail($media);
        $media->update($request->validated());
        $media->load('users');

        return (new MediaResource($media))
            ->additional([
                'message' => 'Media berhasil diperbarui',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $media = Media::findOrFail($id);
        $media->delete();
        return response()->json([
            'message' => 'Media berhasil dihapus'
        ], 200);
    }

    public function userMedia(User $user)
    {
        $media = $user->media()
            ->with('users')
            ->get();

        return MediaResource::collection($media);
    }

    public function attachMedia(User $user, Media $media)
    {
        $alreadyExists = $user->media()
            ->whereKey($media->getKey())
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Media sudah ada pada koleksi pengguna.',
            ], 409);
        }

        $user->media()->attach($media->getKey());
        $media->load('users');

        return (new MediaResource($media))
            ->additional([
                'message' => 'Media berhasil ditambahkan ke koleksi pengguna.',
            ]);
    }

    public function detachMedia(User $user, Media $media)
    {
        $exists = $user->media()
            ->whereKey($media->getKey())
            ->exists();

        if (!$exists) {
            return response()->json([
                'message' => 'Media tidak ditemukan dalam koleksi pengguna.',
            ], 404);
        }

        $user->media()->detach($media->getKey());

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi pengguna.',
        ]);
    }
}
