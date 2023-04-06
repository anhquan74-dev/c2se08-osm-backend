<?php

namespace App\Http\Controllers;

use App\Models\AttachPhoto;
use App\Http\Requests\StoreAttachPhotoRequest;
use App\Http\Requests\UpdateAttachPhotoRequest;

class AttachPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreAttachPhotoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttachPhotoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttachPhoto  $attachPhoto
     * @return \Illuminate\Http\Response
     */
    public function show(AttachPhoto $attachPhoto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AttachPhoto  $attachPhoto
     * @return \Illuminate\Http\Response
     */
    public function edit(AttachPhoto $attachPhoto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAttachPhotoRequest  $request
     * @param  \App\Models\AttachPhoto  $attachPhoto
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttachPhotoRequest $request, AttachPhoto $attachPhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttachPhoto  $attachPhoto
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttachPhoto $attachPhoto)
    {
        //
    }
}
