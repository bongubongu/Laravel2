<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
  public function add()
  {
    return view('admin.profile.create');
  }
  public function create(Request $request)
  {
   
      // Varidationを行う
      $this->validate($request, Profile::$rules);

      $Profile = new Profile;
      $form = $request->all();

      // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
      if (isset($form['image'])) {
        $path = $request->file('image')->store('public/image');
        $Profile->image_path = basename($path);
      } else {
          $Profile->image_path = null;
      }

      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
      // フォームから送信されてきたimageを削除する
      unset($form['image']);

      // データベースに保存する
      $Profile->fill($form);
      $Profile->save();
   
    return redirect('admin/profile/create');
  }
  
  
  public function edit(Request $request)
  {
     $Profile = Profile::find($request->id);
      if (empty($Profile)) {
        abort(404);    
      }
      
      return view('admin.profile.edit', ['Profile_form' => $Profile]);
  }
  
  
  
  public function update(Request $request)
  {
    $this->validate($request, Profile::$rules);
        $Profile = Profile::find($request->id);
        $Profile_form = $request->all();
        if ($request->remove == 'true') {
            $Profile_form['image_path'] = null;
        } elseif ($request->file('image')) {
            $path = $request->file('image')->store('public/image');
            $Profile_form['image_path'] = basename($path);
        } else {
            $Profile_form['image_path'] = $Profile->image_path;
        }

        unset($Profile_form['_token']);
        unset($Profile_form['image']);
        unset($Profile_form['remove']);
        $Profile->fill($Profile_form)->save();

        // 以下を追記
        $history = new ProfileHistory;
        $history->profile_id = $Profile->id;
        $history->edited_at = Carbon::now();
        $history->save();
    
    return redirect('admin/profile/edit');
  }
    
    
    
    
    
        
}
