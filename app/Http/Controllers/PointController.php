<?php
namespace App\Http\Controllers;

use App\Models\Point;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PointController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'type'       => 'required|string',
                'latitude'   => 'required|numeric',
                'longitude'  => 'required|numeric',
                'limitation' => 'required_if:type,panneau|nullable|string', 
            ]);
    
            Point::create($validated);
            
            // dd($validated);
            return Redirect::route('dashboard')->with('success', 'Point ajouté avec succès');
            
        } catch (ValidationException $e) {
            // dd($e->errors());
            return Redirect::back()->with('errors', $e->errors())->withInput();
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return Redirect::back()->withErrors(['general' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }
}
