<?php

namespace App\Http\Controllers;

use App\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Allows the creation of a Promotion as well as the the ability to modify/delete
 */
class PromotionController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getAllPromotions()
    {
        return response()->json(Promotion::all());
    }

    /**
     * @param  string  $client
     * @return JsonResponse
     */
    public function getPromotionByClient($client)
    {
        return response()->json(Promotion::where('client', $client)->get());
    }

    /**
     * @param  string  $client
     * @return JsonResponse
     */
    public function create($client, Request $request)
    {
        $this->validateFields($request);

        $promotion = Promotion::create([
            'client' => $client,
            'entry_fields' => $request->entry_fields,
            'promotion_mechanic' => $request->promotion_mechanic,
        ]);

        return response()->json($promotion, 201);
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function update($id, Request $request)
    {
        $this->validateFields($request);

        $promotion = Promotion::findOrFail($id);
        $promotion->update($request->all());

        return response()->json($promotion, 200);
    }

    /**
     * Delete an individual promotion entry
     * 
     * @param  int  $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        Promotion::findOrFail($id)->delete();
        return response()->json('Deleted Successfully', 200);
    }

    /**
     * Ensure request contains the required fields
     * 
     * @param Request $request
     * @return array
     */
    private function validateFields($request)
    {
        $this->validate($request, [
            'entry_fields' => 'required',
            'promotion_mechanic' => 'required'
        ]);
    }
}