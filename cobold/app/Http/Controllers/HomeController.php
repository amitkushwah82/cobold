<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Expense;
use App\Models\AmountDistribution;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Kolkata');    
    }
    
    /**
     * All user received or pay amount list.
     *
     */
    public function index(){
        $expenses = Expense::all();
        $data = [];
        $users = User::all();
        
        foreach($users as $key){
            foreach(User::where('id','!=',$key->id)->get() as $user){
                $current_user_paid = Expense::join('amount_distributions','amount_distributions.expense_id','=','team_expenses.id')
                ->where('team_expenses.user_id',$key->id)
                ->where('amount_distributions.user_id',$user->id)
                ->sum('amount_distributions.distributed_amount');

                $debtor_user_paid = Expense::join('amount_distributions','amount_distributions.expense_id','=','team_expenses.id')
                ->where('team_expenses.user_id',$user->id)
                ->where('amount_distributions.user_id',$key->id)
                ->sum('amount_distributions.distributed_amount');

                $currentUserName = $key->first_name.' '.$key->last_name;
                $debtorUserName = $user->first_name.' '.$user->last_name;
                $data[$currentUserName][$debtorUserName] = $current_user_paid - $debtor_user_paid;
            
            }
        }

        //print_r($data);die;
        return view('index',compact('data'));
    }

    /**
     * Show team page with all users.
     *
     */
    public function team(){
        $users = User::paginate(10);
        return view('team',compact('users'));
    }

    /**
     * Save new team mates.
     *
     */
    public function teamStore(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try{
            User::create($request->all());
            return redirect()->back()->with('success','Teammate created successfully!');
        }catch(Exception $ex){
            return redirect()->back()->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Show expense page with all expenses.
     *
     */
    public function expenses(){
        $users = User::all();
        $expenses = Expense::paginate(10);
        return view('expenses', compact('expenses', 'users'));
    }

    /**
     * Add user expenses.
     *
     */
    public function expenseStore(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'expense_date' => ['required'],
            'amount' => ['required'],
        ],[
            'user_id.required' => 'Please select a teammate.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try{
            $input = $request->all();
            
            $expense = new Expense();
            $expense->user_id = $input['user_id'];
            $expense->expense_date = date("Y-m-d H:i:s",strtotime($input['expense_date']));
            $expense->amount = $input['amount'];
            $expense->save();

            $user = User::find($input['user_id']);
            $user->total_expenses = $user->total_expenses+$input['amount'];
            $user->save();

            // distributed data in each user
            $users = User::all();
            $userCount = count($users);
            $equalAmount = $input['amount']/$userCount;
            foreach($users as $key){
                $distribution = new AmountDistribution();
                $distribution->user_id = $key->id;
                $distribution->expense_id = $expense->id;
                $distribution->distributed_amount = $equalAmount;
                $distribution->save();
            }

            return redirect()->back()->with('success','Team expense added successfully!');
        }catch(Exception $ex){
            return redirect()->back()->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Get expense data for edit page.
     *
     */
    public function expensesEdit($id){
        $expense = Expense::find($id);
        $expense->expense_date = date('Y-m-d\TH:i', strtotime($expense->expense_date));
        return response()->json(['status'=>true,'expenses'=>$expense]);
    }

    /**
     * Update user expenses.
     *
     */
    public function expensesUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'expense_date' => ['required'],
            'amount' => ['required'],
        ],[
            'user_id.required' => 'Please select a teammate.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator,'edit');
        }

        try{
            $expense = Expense::find($request->id);
            $expense->user_id = $request->user_id;
            $expense->expense_date = date("Y-m-d H:i:s",strtotime($request->expense_date));
            $expense->amount = $request->amount;
            $expense->save();

            // distributed data in each user
            $distributions = AmountDistribution::where('expense_id',$request->id)->get();
            $userCount = count($distributions);
            $equalAmount = $request->amount/$userCount;
            foreach($distributions as $key){
                $distribution = AmountDistribution::find($key->id);
                $distribution->user_id = $request->user_id;
                $distribution->distributed_amount = $equalAmount;
                $distribution->save();
            }

            return redirect()->back()->with('success','Team expense updated successfully!');
        }catch(Exception $ex){
            return redirect()->back()->withInput()->withErrors($ex->getMessage());
        }
    }
}
