<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function index()
    {
        $userId = auth('api')->user()->id;
        $wineList = $this->wine::where('user_id', $userId)->get();
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
        try {
            $wineData = $request->all();
            $userId = auth('api')->user()->id;
            $wineData['user_id'] = $userId;
            $this->wine->create($wineData);
            $result = [
                'message' => 'Vinho inserido com sucesso',
                'status' => 201
            ];
        } catch (\Exception $exception) {
            $result = [
                'message' => 'Houve um erro no registro',
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
            $wine = $this->wine
                ->where('id', $id)
                ->get();
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
        $userId = auth('api')->user()->id;
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
                return response()->json(['message' => 'Você não tem permissão para alterar este dado'], 401);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Vinho não encontrado'], 404);
            }
            return response()->json(['message' => 'Houve um erro na hora de alterar este dado'], 500);
        }
    }
}
