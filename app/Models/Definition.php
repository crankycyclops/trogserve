<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Definition extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'definitions';  

	/**
	 * Attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'author'];
}
