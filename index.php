<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=agung1;AccountKey=Q+6RONB7CyrXDw3rkxnpcMj2hPTcopEdPb8QPOxx0oxTqypE0NPbPTbfgRU6WZQH9SY7V7FCYfe1GNVypR0ANA==";
$containerName = "photo";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
    $fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
    $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
    // echo fread($content, filesize($fileToUpload));
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header("Location: analyze.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>
<!DOCTYPE html >

<html>
	<head>
		<title>Microsoft Azure</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
	<div id="conteneur">
		  <div id="header">My Azure Storage and Computer Vision</div>
		  
		  <div id="haut">
			<ul class="menuhaut">
				<li><a href="">Home</a></li>
			</ul>
		  </div>

		  <div id="centre">
			<h1>My Foto Collection</h1>
			  <h2>Upload Foto</h2>
				  <p>Choose your Foto File </p>
				  <p>Then click UPLOAD for put file into Azure Storage </p>
                  </br>
                  <div class="mt-4 mb-2">
                      <form class="d-flex justify-content-lefr" action="analyze.php" method="post" enctype="multipart/form-data">
                          <input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required=""  >
                          <input type="submit" name="submit" value="Upload" style="background-color:green ">
                      </form>
                  </div>


<!--			  		<table style="border-color: #009FBC"  border="1" width="90%">-->
<!--						<tr>-->
<!--							<td width="5%">No.</td>-->
<!--							<td width="30%">File Name</td>-->
<!--							<td width="50%">File URL</td>-->
<!--							<td width="15%" align="center">Action</td>-->
<!--						</tr>-->
<!---->
<!--						<tr>-->
<!--							<td height="10">1</td>-->
<!--							<td>Name</td>-->
<!--							<td>URL</td>-->
<!--							<td align="center"><button>Analyze</button</td>-->
<!--						</tr>-->
<!--					</table>-->
              <br>
              <br>
              <h2>List of Foto</h2>
              <br>
              <h4>Total Files : <?php echo sizeof($result->getBlobs())?></h4>
              <table class='table table-hover' style="border-bottom: #3882C7" >
                  <thead>
                  <tr>
                      <th>No.</th>
                      <th>File Name</th>
                      <th>File URL</th>
                      <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    $no = 1;
                  do {
                      foreach ($result->getBlobs() as $blob)
                      {
                          ?>
                          <tr>
                              <td align="center"><?php echo $no ?></td>
                              <td><?php echo $blob->getName() ?></td>
                              <td><?php echo $blob->getUrl() ?></td>
                              <td>
                                  <form action="computervision.php" method="post">
                                      <input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
                                      <input type="submit" name="submit" value="Analyze!" class="btn btn-primary" style="background-color: cadetblue ">
                                  </form>
                              </td>
                          </tr>
                          <?php
                          $no=$no+1;
                      }
                      $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                  } while($result->getContinuationToken());
                  ?>
                  </tbody>
              </table>

			</div>
	</div>
	</body>
</html>
