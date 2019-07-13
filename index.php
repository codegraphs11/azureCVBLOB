<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

/**
 * Define connection String from azure
 */

$connectionString = "DefaultEndpointsProtocol=https;AccountName=codegraphs;AccountKey=uNW1oGjEMpsgfk/pysETX1cRfKVvv/IA07ucayZVrgP07FBDzd6SdzMyoSxxrJRjcChL0tvbAFLma+H/LEFg4g==;EndpointSuffix=core.windows.net";

/**
 * Create Connection from client to Azure BLOB
 */

$blobClient = BlobRestProxy::createBlobService($connectionString);

/**
 * Define Container Name
 */

$containerName = "blob-images";

/**
 * submit action for upload image into azure blob
 */
if (isset($_POST['submit'])) {
	$fileToUpload = $_FILES["fileToUpload"]["name"];
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	echo fread($content, filesize($fileToUpload));
		
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}	
	
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">
	<link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Azure Computer Vision</title>
</head>
<body>
    <div class="container">
        <h2>Analisa gambar dengan azure computer vision</h2>
        <form action="index.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
			<input class="form-control" type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
        </div>
        <div class="form-group">
            <input type="submit" name="submit" value="Unggah" class="btn btn-primary">
        </div>
        </form>

        <div class="col-md-12">
            <table class="table">
                <thead>
                    <td>Nama</td>
                    <td>Thumbnail</td>
                    <td>Aksi</td>
                </thead>
                <tbody>
                <?php
                do {
                    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                    foreach ($result->getBlobs() as $key) {
                ?>
                <tr>
                    <td><?php echo $key->getName(); ?></td>
                    <td>
                        <img src="<?php echo $key->getUrl(); ?>" alt="" class="img-thumbnail" width="150px">
                    </td>
                    <td><a href="analitic.php?images=<?php echo $key->getUrl(); ?>" class="btn btn-xs btn-success">Analisa</a></td>
                </tr>
                <?php
                    } $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>