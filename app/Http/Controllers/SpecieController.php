<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecieController extends Controller
{
    use HttpResponses;

    public function index() {
        $spaces = Specie::all();
        return $spaces;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:species|max:50'
            ]);

            $data = $request->all();

            $space = Specie::create($data);

            return $space;
        } catch (Exception $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
