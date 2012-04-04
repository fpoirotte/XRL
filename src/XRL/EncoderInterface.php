<?php

interface XRL_EncoderInterface
{
    /// Make the output compact.
    const OUTPUT_COMPACT    = 0;

    /// Make the output pretty.
    const OUTPUT_PRETTY     = 1;

    public function encodeRequest(XRL_Request $request);
    public function encodeError(Exception $error);
    public function encodeResponse($response);
}

