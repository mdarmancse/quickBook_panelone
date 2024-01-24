<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marchant;

class MarchantController extends Controller
{
    public function index()
    {
        $data = [];
        $marchants = Marchant::all();
        $data['menu'] = "marchant";
        $data['menu_sub'] = "";
        $data['marchants'] = $marchants;
        return view('marchants.index', $data);
       // return view('marchants.index', compact('data'));
    }

    public function create()
    {
        $data['menu'] = "marchant";
        $data['menu_sub'] = "";
        return view('marchants.create',$data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:marchants',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            Marchant::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Hash the password
            ]);

            return redirect()->route('marchants.index')->with('success', 'Marchant created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }
    }

    public function edit($id)
    {
        $marchant = Marchant::findOrFail($id);
        $data['menu'] = "marchant";
        $data['menu_sub'] = "";
        $data['marchant'] = $marchant;

        return view('marchants.edit', $data);
       // return view('marchants.edit', compact('merchant'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:marchants,email,' . $id,
            'password' => 'required|min:6|confirmed',
        ]);


        try {
            $merchant = Marchant::findOrFail($id);
            $merchant->update($request->all());
            return redirect()->route('marchants.index')->with('success', 'Marchant updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }


    }

    public function destroy($id)
    {
        $merchant = Marchant::findOrFail($id);
        $merchant->delete();

        return redirect()->route('marchants.index')->with('success', 'Marchant deleted successfully');
    }
}



?>
