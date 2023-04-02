<?php

namespace App\Http\Controllers;

use App\Models\RoleDetails;
use App\Http\Requests\StoreRoleDetailsRequest;
use App\Http\Requests\UpdateRoleDetailsRequest;

class RoleDetailsController extends Controller
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
     * @param  \App\Http\Requests\StoreRoleDetailsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleDetailsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RoleDetails  $roleDetails
     * @return \Illuminate\Http\Response
     */
    public function show(RoleDetails $roleDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RoleDetails  $roleDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(RoleDetails $roleDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRoleDetailsRequest  $request
     * @param  \App\Models\RoleDetails  $roleDetails
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleDetailsRequest $request, RoleDetails $roleDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RoleDetails  $roleDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoleDetails $roleDetails)
    {
        //
    }
}
