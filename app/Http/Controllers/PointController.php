<?php
namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

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
            
            return Redirect::route('gestion')->with('success', 'Point ajouté avec succès');
            
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['general' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }

    public function index()
    {
        $points = Point::all();

        return Inertia::render('map', [
            'points' => $points,
        ]);
    }

    public function gestion(Request $request)
    {
        $query = Point::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $points = $query->get();

        return Inertia::render('gestionPoint', [
            'points' => $points,
            'filters' => $request->only(['search']),
        ]);
    }

    public function destroy($id)
    {
        $point = Point::findOrFail($id);
        $point->delete();

        return Redirect::route('gestion')->with('success', 'Point supprimé avec succès');
    }

    public function update(Request $request, $id)
    {
        $point = Point::findOrFail($id);
        try {
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'type'       => 'required|string',
                'latitude'   => 'required|numeric',
                'longitude'  => 'required|numeric',
                'limitation' => 'required_if:type,panneau|nullable|string', 
            ]);
    
            $point->update($validated);
            
            return Redirect::route('gestion')->with('success', 'Point modifié avec succès');
            
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['general' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }

    public function edit($id)
    {
        return Inertia::render('update', [
            'point' => Point::findOrFail($id),
        ]);
    }
}
