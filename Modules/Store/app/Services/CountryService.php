<?php

namespace Modules\Store\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Store\Models\Country;
use Yajra\DataTables\DataTables;

class CountryService
{
    public function getCountryDataTable(Request $request)
    {
        $query = Country::query()->orderBy('name');

        return DataTables::of($query)
            ->addColumn('action', function (Country $country) {
                return view('components.action-buttons', [
                    'id' => $country->id,
                    'edit' => 'countryEdit',
                    'delete' => 'countryDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveCountry(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $countryId = $data['country_id'] ?? null;
                unset($data['country_id']);

                if ($countryId) {
                    $country = Country::findOrFail($countryId);
                    $country->update($data);
                    $message = 'Country updated successfully.';
                } else {
                    $country = Country::create($data);
                    $message = 'Country created successfully.';
                }

                return [
                    'status' => 'success',
                    'message' => $message,
                    'country' => $country->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving country: ' . $e->getMessage(),
            ];
        }
    }

    public function getCountryById(int $id): array
    {
        try {
            $country = Country::findOrFail($id);
            return [
                'status' => 'success',
                'country' => $country,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Country not found.',
            ];
        }
    }

    public function deleteCountry(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $country = Country::findOrFail($id);
                $country->delete();
                return [
                    'status' => 'success',
                    'message' => 'Country deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error deleting country: ' . $e->getMessage(),
            ];
        }
    }

    public function getAllCountries(): array
    {
        return Country::orderBy('name')->get()->toArray();
    }
}