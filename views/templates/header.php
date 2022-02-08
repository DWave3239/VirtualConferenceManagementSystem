<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Bacc Mockup Conference Site index</title>

  <script type="module">
    document.documentElement.classList.remove('no-js');
    document.documentElement.classList.add('js');
  </script>

  <base href="{$base_url}">

  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/print.css" media="print">

  <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
  <!-- font awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
  
  <meta name="description" content="Bachelor Thesis Mockup Project">
  <meta property="og:title" content="Bacc Mockup Conference Site index">
  <meta property="og:description" content="Bachelor Thesis Mockup Project">
  <meta property="og:image" content="{$base_url}/assets/images/og_image.jpg">
  <meta property="og:image:alt" content="Image description">
  <meta property="og:locale" content="en_GB">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta property="og:url" content="index.php?main/index">
  <link rel="canonical" href="index.html?main/index">

  <link rel="icon" href="favicon.ico">
  <link rel="icon" href="favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="manifest" href="my.webmanifest">
  <meta name="theme-color" content="#0dcaf0">
  <script>
    function createScheduler(id, enableHighlightUpdates){
        if(typeof Scheduler2 == "function"){
            new Scheduler2(id, enableHighlightUpdates);
            console.log("scheduler for id "+id+" has been instantiated");
        }else{
            setTimeout(function(){createScheduler(id, enableHighlightUpdates);}, 1000);
        }
    }
  </script>
</head>
{include="body"}