<?php

namespace App\Http\Controllers;

use App\Mail\SendWelcomePet;
use App\Models\Adoption;
use App\Models\Client;
use App\Models\People;
use App\Models\Pet;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class AdoptionController extends Controller
{
    use HttpResponses;
    public function indexAdoption(Request $request)
    {
        // Construindo a query base
        $query = Adoption::query();

        // Filtrando por nome, se fornecido
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filtrando por email, se fornecido
        if ($request->filled('email')) {
            $query->where('email', $request->email);
        }

        // Filtrando por contato, se fornecido
        if ($request->filled('contact')) {
            $query->where('contact', 'like', '%' . $request->contact . '%');
        }

        // Filtrando por status da adoção, se fornecido
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Obtendo os resultados filtrados
        $adocoes = $query->get();

        // Retornando os resultados (ajuste conforme sua necessidade, ex.: view, json)
        return response()->json($adocoes);
    }
    public function index(Request $request)
    {
        try {

            // pegar os dados que foram enviados via query params
            $filters = $request->query();

            // inicializa uma query
            $pets = Pet::query()
                ->select(
                    'id',
                    'pets.name as pet_name',
                    'pets.age as age'
                )
                #->with('race') // traz todas as colunas
                ->where('client_id', null);


            // verifica se filtro
            if ($request->has('name') && !empty($filters['name'])) {
                $pets->where('name', 'ilike', '%' . $filters['name'] . '%');
            }

            if ($request->has('age') && !empty($filters['age'])) {
                $pets->where('age', $filters['age']);
            }

            if ($request->has('size') && !empty($filters['size'])) {
                $pets->where('size', $filters['size']);
            }

            if ($request->has('weight') && !empty($filters['weight'])) {
                $pets->where('weight', $filters['weight']);
            }

            if ($request->has('specie_id') && !empty($filters['specie_id'])) {
                $pets->where('specie_id', $filters['specie_id']);
            }

            return $pets->orderBy('created_at', 'desc')->get();
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        $pet = Pet::with("race")->with("specie")->find($id);

        if ($pet->client_id) return $this->error('Dados confidenciais', Response::HTTP_FORBIDDEN);

        if (!$pet) return $this->error('Dado não encontrado', Response::HTTP_NOT_FOUND);

        return $pet;
    }

    public function store( Request $request){
        try {
            // rebecer os dados via body
            $data = $request->all();

            $request->validate([
                'name' => 'required|string|max:150',
                'contact' => 'int',
                'email' => 'email',
                'cpf' => 'required|string',
                'observations' => 'required|int',
                'profile_id' => 'required|int',

            ]);

            $adoption = Adoption::create(...$data, ['status' => 'PENDENTE']);

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function approved(Request $request){

        $data = $request->all();

        $request->validate([
            'adoption_id' => 'intege|required',
        ]);

        $adoption = Adoption::find($data['$adoption_id']);

        if (!$adoption) return $this->error('Dado não encontrado', Response::HTTP_NOT_FOUND);

        $adoption->update(["status" => 'APROVADO']);
        $adoption->save();

       $people = People::create([

            'name' => $adoption->name,
            'email' => $adoption->email,
            'cpf' => $adoption->cpf,
            'contact' => $adoption->contact,

        ]);

        $client = Client::create([
            "people_id" => $people->id,
            'bonus' => true,
        ]);

        $pet = Pet::find($adoption->Pet->id);

        $pet->update(["client_id" => $client->id]);
    }
}


