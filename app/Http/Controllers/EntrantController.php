<?php

namespace App\Http\Controllers;

use App\Entrant;
use App\Promotion;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
        // Todo: Move validation to form request (separate class ChanceRequest)
        $data = $request->all();

        if (!array_key_exists('client', $data)) {
            return response()->json(
                "A client must be specified. Entry not submitted."
            );
        }

        $promotion = Promotion::where('client', $data['client'])->first();

        $this->validateFields($request, $promotion);

        if ($this->checkEmailAlreadyEntered($data['email'])) {
            return response()->json(
                "Entrant email has already been entered into the promotion. Entry not submitted."
            );
        }

        $requiredFields = explode(',', $promotion->entry_fields);
        $submittedFields = array_keys($data);

        if (!empty(array_diff($submittedFields, $requiredFields))) {
            return response()->json(
                "This Promotion requires the following fields to
                be submitted: " . $promotion->entry_fields . ". Entry not submitted."
            );
        }

        $mechanism = $promotion->getMechanism();
        if ($mechanism !== 'winning-moment') {
            return response()->json(
                "This Promotion is only accepting winning moment submissions, not ${mechanism} submissions."
                    . " Please use the correct endpoint. Entry not submitted."
            );
        }

        $winningMomentTime = $promotion->getWinningMomentTime();
        $timeOfEntry = date("Y-m-d H:i:s");

        $winnerDrawn = $promotion->getWinnerDrawn();

        if ($timeOfEntry < $winningMomentTime) {
            Entrant::create([
                'client' => $data['client'],
                'name' => Crypt::encryptString($data['name']),
                'email' => Crypt::encryptString($data['email']),
            ]);

            $promotion->incrementEntryCount();

            return response()->json("Time of entry is before winning moment time. Not a winner.");
        }

        if ($winnerDrawn) {
            Entrant::create([
                'client' => $data['client'],
                'name' => Crypt::encryptString($data['name']),
                'email' => Crypt::encryptString($data['email']),
            ]);

            $promotion->incrementEntryCount();

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
        // Todo: Move validation to form request (separate class ChanceRequest)
        $data = $request->all();

        if (!array_key_exists('client', $data)) {
            return response()->json(
                "A client must be specified. Entry not submitted."
            );
        }

        $promotion = Promotion::where('client', $data['client'])->first();

        $this->validateFields($request, $promotion);

        if ($this->checkEmailAlreadyEntered($data['email'])) {
            return response()->json(
                "Entrant email has already been entered into the promotion. Entry not submitted."
            );
        }

        $mechanism = $promotion->getMechanism();
        if ($mechanism !== 'chance') {
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
                'name' => Crypt::encryptString($data['name']),
                'email' => Crypt::encryptString($data['email']),
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
     * @param $email
     * @return mixed
     */
    private function checkEmailAlreadyEntered($email)
    {
        return Entrant::where('email', '=', $email)->exists();
    }
}
