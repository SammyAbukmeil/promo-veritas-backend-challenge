<?php

namespace App\Http\Controllers;

use App\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Allows the creation of a Promotion as well as the the ability to modify/delete
 */
class PromotionController extends Controller
{
    /**
     * Flag to indicate that a winner hasn't been drawn for this promotion
     */
    const WINNER_NOT_DRAWN = 0;

    /**
     * All promotions start with 0 entries
     */
    const START_ENTRY_COUNT = 0;

    /**
     * @return JsonResponse
     */
    public function getAllPromotions(): JsonResponse
    {
        return response()->json(Promotion::all());
    }

    /**
     * @param string $client
     * @return JsonResponse
     */
    public function getPromotionByClient(string $client): JsonResponse
    {
        // Todo: Use route model binding to automatically get the client from the URL
        // Todo: get() returns a collection/array, can use first() to get the first result
        // Todo: What if a client has multiple promotions? Perhaps getPromotionById

        return response()->json(Promotion::where('client', $client)->get());
    }

    /**
     * @param string $client
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(string $client, Request $request): JsonResponse
    {
        $this->validateFields($request);

        $promotion = Promotion::create([
            'client' => $client,
            'entry_fields' => $request->entry_fields,
            'promotion_mechanic' => $request->promotion_mechanic,
            'winning_moment_time' => $request->winning_moment_time,
            'winner_drawn' => self::WINNER_NOT_DRAWN,
            'entry_count' => self::START_ENTRY_COUNT
        ]);

        return response()->json($promotion, 201);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $this->validateFields($request);

        $promotion = Promotion::findOrFail($id);
        $promotion->update($request->all());

        return response()->json($promotion);
    }

    /**
     * Delete an individual promotion entry
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        Promotion::findOrFail($id)->delete();

        return response()->json('Deleted Successfully');
    }

    /**
     * Ensure request contains the required fields
     *
     * @param Request $request
     * @throws ValidationException
     */
    private function validateFields(Request $request)
    {
        $this->validate($request, [
            'entry_fields' => 'required',
            'promotion_mechanic' => 'required'
        ]);
    }
}
