<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        // {
        //     "name": "test",
        //     "type": "PF",
        //     "identification": "12",
        //     "categories": [
        //         {
        //             "phone": "123",
        //             "email": "test@gmail",
        //             "cellphone": "54652"
        //         },
        //         {
        //             "phone": "1233",
        //             "email": "test@g3mail",
        //             "cellphone": "54653"
        //         }
        //     ]
        // }

        $identification = Customer::where('identification', $request->get('identification'))->get()->toArray();

        if(count($identification) > 0) {
            return response()->json(['message' => 'Customer already registered'], 404);
        }

        $customer = Customer::create($request->only('name', 'type', 'identification'));
        $customer->contacts()->createMany($request->get('categories'));
        $result = $customer;
        $result = $result->contacts;

        return response()->json($customer, 201);
    }
}