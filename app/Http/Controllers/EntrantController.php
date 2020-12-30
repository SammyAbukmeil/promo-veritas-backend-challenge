<?php

namespace App\Http\Controllers;

use App\Entrant;
use App\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Accepts Entrant submissions for Promotions, and selects/notifies winners
 */
class EntrantController extends Controller
{
    /**
     * Checks if the provided entrant is a winner for the Winning Moment mechanism
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function checkWinningMomentWinner(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->validateRequest($request);

        $promotion = $this->getPromotion($data['client']);

        $this->validateFields($request, $promotion);

        $mechanism = $promotion->getMechanism();
        if ($mechanism !== Promotion::WINNING_MOMENT_MECHANISM_NAME) {
            return response()->json(
                "This Promotion is only accepting winning moment submissions, not ${mechanism} submissions."
                    . " Please use the correct endpoint. Entry not submitted."
            );
        }

        $winningMomentTime = $promotion->getWinningMomentTime();
        $timeOfEntry = date("Y-m-d H:i:s");

        if ($timeOfEntry < $winningMomentTime) {
            $this->createNonWinningEntrant($data, $promotion);

            return response()->json("Time of entry is before winning moment time. Not a winner.");
        }

        $winnerDrawn = $promotion->getWinnerDrawn();

        if ($winnerDrawn) {
            $this->createNonWinningEntrant($data, $promotion);

            return response()->json("A winner has already been drawn for this promotion. Not a winner.");
        }

        // Winner, save details without anonymising
        Entrant::create([
            'client' => $data['client'],
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $promotion->setWinnerDrawn();

        mail($data['email'], "You won!", "Click this link...");

        return response()->json("Winner, please check email for details.");
    }

    /**
     * Checks if the provided entrant is a winner for the Chance mechanism
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function checkChanceWinner(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->validateRequest($request);

        $promotion = $this->getPromotion($data['client']);

        $this->validateFields($request, $promotion);

        $mechanism = $promotion->getMechanism();
        if ($mechanism !== Promotion::CHANCE_MECHANISM_NAME) {
            return response()->json(
                "This Promotion is only accepting chance submissions, not ${mechanism} submissions."
                . " Please use the correct endpoint. Entry not submitted."
            );
        }

        $promotion->incrementEntryCount();

        $entryCount = $promotion->getEntryCount();

        if ($entryCount % 5 !== 0) {
            Entrant::create([
                'client' => $data['client'],
                'name' => Hash::make($data['name']),
                'email' => Hash::make($data['email']),
            ]);

            mail($data['email'], "Unsuccessful", "Thanks for entring, better luck next time!");

            return response()->json("Entry count isn't a multiple of 5. Not a winner.");
        }

        // Winner, save details without anonymising
        Entrant::create([
            'client' => $data['client'],
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        mail($data['email'], "You Won!", "Click this link...");

        return response()->json("Winner, please check email for details.");
    }

    /**
     * Ensure request contains the required fields
     *
     * @param Request $request
     * @param Promotion $promotion
     * @throws ValidationException
     */
    private function validateFields(Request $request, Promotion $promotion)
    {
        $promotionRequiredFields = explode(',', $promotion->entry_fields);

        $validationFields = [];

        foreach ($promotionRequiredFields as $field) {
            $validationFields[$field] = 'required';
        }

        $this->validate($request, $validationFields);
    }

    /**
     * @param array $data
     * @param Promotion $promotion
     */
    private function createNonWinningEntrant(array $data, Promotion $promotion): void
    {
        Entrant::create([
            'client' => $data['client'],
            'name' => Hash::make($data['name']),
            'email' => Hash::make($data['name']),
        ]);

        $promotion->incrementEntryCount();
    }

    /**
     * @param $client
     * @return Promotion $promotion
     */
    private function getPromotion($client): Promotion
    {
        $promotion = Promotion::where('client', $client)->first();

        if (!$promotion) {
            abort(404);
        }

        return $promotion;
    }

    /**
     * @param $request
     * @return JsonResponse|array
     * @throws ValidationException
     */
    private function validateRequest($request)
    {
        if (!$this->validate($request, ['client' => 'required'])) {
            return response()->json(
                "A client must be specified. Entry not submitted.", 422
            );
        }
    }
}
