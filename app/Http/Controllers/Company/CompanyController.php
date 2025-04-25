<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Deploy;
use App\Models\Warehouse\RequestOrder;
use App\Models\Warehouse\Stock;

class CompanyController extends Controller
{
    public function index()
    {

        return view('company.index');

    }
    public function getLeadmanNames()
    {
        $leadmanNames = DB::table('users')->where('role_id', 2)->pluck('firstname', 'id');
        // Assuming role_id 2 corresponds to leadmen in your database.

        return $leadmanNames;
    }
    public function getCurrentUserRole()
    {
        $user = auth()->user();
        $role = $user->role_id;
        $id = $user->id;
        $firstName = $user->firstname; // Assuming the column name is 'firstname'
        $lastName = $user->lastname; // Assuming the column name is 'lastname'
        $middleName = $user->middlename;
        
        return response()->json(['role' => $role, 'id' => $id, 'firstname' => $firstName, 'lastname' => $lastName, 'middlename' => $middleName]);
    }

    public function disapproveRequest(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
    
        // Check if the request is not already disapproved
        if (!$requestOrder->is_disapproved) {
            $requestOrder->update(['is_disapproved' => 1]);
        }
    
        return response()->json(['success' => true]);
    }
    
    public function getStockData($id)
    {
        $stock = Stock::find($id);
    
        if (!$stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
    
        // Check if the quantity is zero
        if ($stock->quantity == 0) {
            return response()->json(['message' => 'Sorry']);
        }
    
        // Return the stock data
        return response()->json($stock);
    }
    

    public function approveRequest(Request $request, $id)
    {
        $requestOrder = RequestOrder::findOrFail($id);
        
        // Check if the request is not already approved
        if (!$requestOrder->is_approved) {
            // Assuming you're using dependency injection to get an instance of the Stock model
            $stock = new Stock();
            
            // Check if the stock_id matches the one in the approved request
            $stockItem = $stock->find($requestOrder->stock_id);
            
            if ($stockItem) {
                // Check if the stock quantity is less than the requested quantity
                if ($stockItem->quantity < $requestOrder->quantity) {
                    return response()->json(['error' => 'Insufficient stock quantity'], 400);
                }
    
                // Proceed with approving the request and updating the stock
                $requestOrder->update(['is_approved' => 1, 'is_disapproved' => 0]);
    
                // Update the is_approved field in the Deploy model directly in the database
                Deploy::where('stock_id', $requestOrder->stock_id)
                    ->where('item_id', $requestOrder->item_id)
                    ->where('area_id', $requestOrder->area_id)
                    ->where('date', $requestOrder->date)
                    ->update(['is_approved' => 1]);
    
                // Deduct quantity and update used quantity
                $stockItem->quantity -= $requestOrder->quantity;
                $stockItem->used += $requestOrder->quantity;
                
                // Check if the quantity is 0 and update the is_quantity field in RequestOrder model
                if ($stockItem->quantity == 0) {
                    RequestOrder::where('stock_id', $stockItem->id)->update(['is_quantity' => 1]);
                }
    
                $stockItem->save();
            }
        }
        
        return response()->json(['success' => true]);
    }

    public function getRemainingQuantityAttribute()
    {
        // Calculate the remaining quantity based on the quantity and used quantity
        return $this->stock->quantity - $this->used;
    }
    

public function deducQuantityAddUsed(Request $request)
{
    // Assuming you're using dependency injection to get an instance of the Stock model
    $stock = new Stock();

    $data = $stock->find($request->id);
    $data->quantity -= $request->quantity;
    $data->used += $request->quantity;
    if ($data->save()) {
        return 'success';
    }
}

// Within your controller or wherever you need to check the quantity

    
    public function getLeadmans()
    {
        // get all staffs from users table but also add the role name using role_id
        $leadmans = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')->get();
        
        error_log(json_encode($leadmans));
        return response()->json($leadmans);
    }

    public function getLeadmanById($id)
    {
        $leadman = DB::table('users')->where('id', $id)->first();
        return response()->json($leadman);
    } 
    public function updateLeadman(Request $request, $id)
{
    $leadman = DB::table('users')->where('id', $id)->first();

    if (!$leadman) {
        return response()->json(['error' => 'Leadman not found'], 404);
    }

    // Update the leadman's information
    $leadmanUpdate = DB::table('users')
        ->where('id', $id)
        ->update([
            'firstname' => $request->input('firstname', $leadman->firstname),
            'lastname' => $request->input('lastname', $leadman->lastname),
            'middlename' => $request->input('middlename', $leadman->middlename),
            'username' => $request->input('username', $leadman->username),
            'email' => $request->input('email', $leadman->email),
            'password' => bcrypt($request->input('password', $leadman->password)),
            'role_id' => $request->input('position_id', $leadman->role_id),
            'updated_at' => now()
        ]);

    if ($leadmanUpdate) {
        return response()->json(['message' => 'Leadman updated successfully'], 200);
    } else {
        return response()->json(['error' => 'Failed to update leadman'], 500);
    }
}

    public function deleteLeadman($id){
        $leadman = DB::table('users')->where('id', $id)->delete();
        return response()->json($leadman);
    }

    public function createLeadman(Request $request){

        $leadman = DB::table('users')->insert([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middlename' => $request->middlename,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->position_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json($leadman, 200);

    }

    public function createJob()
    {

        return view('company.create_job');

    }

    public function jobs()
    {

        return view('company.jobs');

    }

    public function applicants()
    {

        return view('company.applicants');

    }

    public function profile()
    {

        return view('company.profile');

    }
}
