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
	 * We'll let the database set and update our timestamps directly (see
	 * migrations/2020_02_02_062438_api_definitions.php.)
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'author'];
}
