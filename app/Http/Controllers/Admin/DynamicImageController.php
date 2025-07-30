<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateImage;

class DynamicImageController
{
    public function index()
    {
        $url = GenerateImage::run("MEME Project Update: 旺柴 Makes Remarkable Progress",
            "The Green Hat Boy has significantly driven the growth of the Chinese MEME project 旺柴, with official support from Bonk. This week, 旺柴 has entered the buyback list, experiencing rapid growth once again. It continues to hold the potential as the leading Chinese MEME dog.");
        return '<img src="' . $url . '">';
    }
}
