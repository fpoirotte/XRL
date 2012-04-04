<?php

interface XRL_DecoderInterface
{
    public function decodeRequest($data);
    public function decodeResponse($data);
}

