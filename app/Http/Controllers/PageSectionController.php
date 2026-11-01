<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageSectionRequest;
use App\Http\Requests\UpdatePageSectionRequest;
use App\Models\PageSection;

class PageSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageSectionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PageSection $pageSection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageSectionRequest $request, PageSection $pageSection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PageSection $pageSection)
    {
        //
    }
}
