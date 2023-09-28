<?php

namespace Plugse\Ctrl\helpers;

trait Services
{
    public function hideEmail(string $email): string
    {
        $arrayMail = explode('@', $email);
        $initMail = substr($arrayMail[0], 0, 2);

        return "{$initMail}*****@{$arrayMail[1]}";
    }

    public function convertObjectInArray(object $object): array
    {
        $response = [];
        $className = get_class($object);
        foreach ((array) $object as $key => $value) {
            $key = trim(substr($key, strlen($className) + 1));
            $response[$key] = !is_object($value)
                ? $value
                : $this->convertObjectInArray($value);
        }

        return $response;
    }

    public function createHtml(string $tag, string $text, $attributes = [])
    {
        $inlineTags = ['img', 'hr'];

        $result = "<$tag";
        foreach ($attributes as $atribute => $value) {
            $result .= " {$atribute}=\"{$value}\"";
        }
        if (in_array($tag, $inlineTags)) {
            $result .= ' />';
        } else {
            $result .= ">{$text}</{$tag}>";
        }


        return $result;
    }


    public function getHtmlString(string $title, string $body)
    {
        return "
        <html>
        <head>
            <title>{$title}</title>
            <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body{
                font-family: Verdana, Geneva, Tahoma, sans-serif;
                color: #3A3A3A;
            }
            
            h1{
                background: #3A3A3A;
                color: #E1E1E1;
                font-size: 1.6rem;
                font-weight: 600;
                padding: .8rem;
            }
            
            h2{
                font-size: 1.2rem;
                font-weight: 600;
                padding: .8rem;
                margin: .8rem 0;
            }
            
            h3{
                font-size: 1.2rem;
                font-weight: 400;
                border-bottom: solid #000 1px;
            }
            
            p{
                font-size: 1.2rem;
                font-weight: 400;
                margin-bottom: 1.2rem;
            }
            
            a{
                color: #3A3A3A;
                text-decoration: underline;
                font-size: inherit;
                font-weight: 600;
                transition: transform .5s;
            }
            
            a:hover{
                transform: scale(1.2);
            }
            
            strong{
                font-size: inherit;
                color: inherit;
                font-weight: 700;
            }
            </style>
        </head>
        <body>{$body}</body>
    </html>
        ";
    }
}
