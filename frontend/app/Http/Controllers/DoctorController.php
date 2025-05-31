<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
{
    $doctors = $this->api->get('doctors')->json();
    return view('doctors.index', compact('doctors'));
}

}
