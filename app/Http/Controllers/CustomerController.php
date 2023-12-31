<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('contacts')->get();
        return CustomerResource::collection($customers);
    }

    public function store(Request $request)
    {
        $result = Customer::where('identification', $request->get('identification'))->get()->toArray();

        if(count($result) > 0) {
            return response()->json(['message' => 'Customer with identification already registered'], 404);
        }

        $customer = Customer::create($request->only('name', 'type', 'identification'));
        $customer->contacts()->createMany($request->get('contacts'));
        $result = $customer;
        $result = $result->contacts;

        return response()->json($customer, 201);
    }

    public function show(string $id)
    {
        $customer = Customer::with('contacts')->find($id);

        if(!$customer) {
            return response()->json(['data' => ['message' => 'Customer not exists']], 404);
        }

        return CustomerResource::make($customer);
    }

    public function delete(string $id)
    {
        $customer = Customer::find($id)->get();
        Customer::destroy($customer[0]->id);

        return response()->json(['message' => 'Customer deleted with success.'], 201);
    }

    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id)->get()->toArray();

        if(count($customer) == 0) {
            return response()->json(['message' => 'Customer not exists'], 404);
        }

        Customer::find($id)->update($request->only('name', 'type', 'identification'));
        foreach ($request->get('contacts') as $item) {
            Contact::find($item['id'])->update([
                'phone' => $item['phone'],
                'email' => $item['email'],
                'cellphone' => $item['cellphone']
            ]);
        }

        return response()->json(Customer::with('contacts')->find($customer[0]['id']), 201);
    }
}
