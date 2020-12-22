<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Wine;

class WineController extends Controller
{
    private $wine;

    function __construct(Wine $wine)
    {
        $this->wine = $wine;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = $request->input('orderBy');
        $direction = $request->input('direction');
        $searchTerm = $request->input('searchTerm');
        $userId = auth('api')->user()->id;
        if ($orderBy) {
            $wineList = $this->wine::where('user_id', $userId)
                ->where('name', 'like', '%' . $searchTerm . '%')
                ->orderBy($orderBy, $direction)
                ->paginate(6);
            $wineList->appends($_GET)->links();
        } else {
            $wineList = $this->wine::where('user_id', $userId)
                ->where('name', 'like', '%' . $searchTerm . '%')
                ->paginate(6);
        }
        return $wineList;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $wineData = $request->all();
        $rules =  [
            'name' => 'required|string|max:180',
            'year' => 'required|numeric|gte:0|lte:' . now()->format('Y'),
            'description' => 'required|string|max:255',
            'grade' => 'required|numeric|gte:0|lte:10'
        ];
        $validate = Validator::make($wineData, $rules);

        if ($validate->fails()) {
            return response()->json(
                [
                    'message' => 'Erro na validação',
                    'errors' => $validate->errors()->all()
                ],
                422
            );
        }
        try {
            $userId = auth('api')->user()->id;
            $wineData['user_id'] = $userId;
            $this->wine::create($wineData);
            $result = [
                'message' => 'Vinho inserido com sucesso',
                'status' => 201
            ];
        } catch (\Exception $exception) {
            $result = [
                'message' => $exception,
                'status' => 500
            ];
        }
        return response()->json($result, $result['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $userId = auth('api')->user()->id;
            $wine = $this->wine::where('id',$id)
                ->where('user_id',$userId)
                ->first();
            if(!$wine) 
                return response()->json(['message' => 'Vinho não encontrado'],404);
            $result = ['data' => $wine, 'status' => 200];
        } catch (\Exception $exception) {
            $result = ['message' => 'Houve um erro ao buscar pelo vinho', 'status' => 500];
        }
        return response()->json($result, $result['status']);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updatedWineData = $request->all();
        $wineData = $request->all();
        $rules =  [
            'name' => 'required|string|max:180',
            'year' => 'required|numeric|gte:0|lte:' . now()->format('Y'),
            'description' => 'required|string|max:255',
            'grade' => 'required|numeric|gte:0|lte:10'
        ];
        $validate = Validator::make($wineData, $rules);
        $userId = auth('api')->user()->id;
        if($validate->fails()) {
             return response()->json(
                [
                    'message' => 'Erro na validação',
                    'errors' => $validate->errors()->all()
                ],
                422
            );
        }
        try {
            $wine = $this->wine->findOrFail($id);
            if ($wine['user_id'] === $userId) {
                $wine->fill($updatedWineData);
                $wine->save();
                return response()->json(['message' => 'Vinho atualizado com sucesso']);
            } else {
                return response()->json(['message' => 'Você não tem permissão para alterar este dado'], 401);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Vinho não encontrado'], 404);
            }
            return response()->json(['message' => 'Houve um erro na hora de alterar este dado'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try {
            $wine = $this->wine->findOrFail($id);
            $userId = auth('api')->user()->id;
            if ($wine['user_id'] === $userId) {
                $wine->delete();
                return response()->json(['message' => 'Vinho deletado com sucesso']);
            } else {
                return response()->json(['message' => 'Você não tem permissão para alterar este dado'], 403);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Vinho não encontrado'], 404);
            }
            return response()->json(['message' => 'Houve um erro na hora de alterar este dado'], 500);
        }
    }
}
