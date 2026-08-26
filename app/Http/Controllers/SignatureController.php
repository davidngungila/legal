<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'signature_data' => 'required_without:signature_file|string',
            'signature_file' => 'required_without:signature_data|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $directory = $request->input('directory', 'signatures');
            $prefix = $request->input('prefix', 'sig');
            $fileName = $prefix . '_' . time() . '_' . Str::random(6) . '.png';

            if ($request->has('signature_data') && $request->signature_data) {
                $signatureData = $request->signature_data;
                $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $signatureData);
                $signatureImage = base64_decode($signatureData);

                if ($signatureImage === false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid signature data'
                    ], 422);
                }

                $path = $directory . '/' . $fileName;
                Storage::disk('public')->put($path, $signatureImage);

            } elseif ($request->hasFile('signature_file')) {
                $file = $request->file('signature_file');
                $fileName = $prefix . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($directory, $fileName, 'public');

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No signature data provided'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Signature saved successfully',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ]);

        } catch (\Exception $e) {
            \Log::error('Signature save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save signature: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeBase64(Request $request, string $directory, string $prefix)
    {
        $validator = \Validator::make($request->all(), [
            'signature_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fileName = $prefix . '_' . time() . '_' . Str::random(6) . '.png';
            $signatureData = $request->signature_data;
            $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $signatureData);
            $signatureImage = base64_decode($signatureData);

            if ($signatureImage === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature data'
                ], 422);
            }

            $path = $directory . '/' . $fileName;
            Storage::disk('public')->put($path, $signatureImage);

            return response()->json([
                'success' => true,
                'path' => $path,
            ]);

        } catch (\Exception $e) {
            \Log::error('Signature save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save signature'
            ], 500);
        }
    }
}
