<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Carbon\Carbon;

class PhoneVerificationController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        $request->validate(['phone' => 'required|numeric|regex:/^[0-9]{10,15}$/']);

        $code = rand(100000, 999999);

        DB::table('phone_verifications')->insert([
            'phone' => $request->phone,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $client->messages->create($request->phone, [
            'from' => config('services.twilio.from'),
            'body' => 'Your verification code is ' . $code,
        ]);

        return response()->json(['message' => 'Verification code sent!']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|regex:/^[0-9]{10,15}$/',
            'code' => 'required|digits:6',
        ]);

        $verification = DB::table('phone_verifications')
            ->where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->first();

        if ($verification) {
            $request->user()->update(['phone_verified_at' => Carbon::now()]);
            return response()->json(['message' => 'Phone verified successfully.']);
        }

        return response()->json(['message' => 'Invalid verification code.'], 422);
    }
}
