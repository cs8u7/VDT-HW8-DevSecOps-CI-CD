## HW8-DevSecOps-CI-CD

- Name: Tran Duc Tuan - VDT 2024
- Start dockers command: `sudo docker compose up --build
- URL access: `http://localhost:4000/`

## Vulnerability Analysis

### Unrestricted File Upload (/edit.php)

![Pasted image 20240517115433.png](/home/tieudaodaide/Documents/Viettel/HW8-DevSecOps-CI-CD/report_img/Pasted image 20240517115433.png)

- In server side, it just check file's size to attacker and easy upload a php shell to server

```PHP
<?php phpinfo(); ?>
```

- Success upload shell
  ![Image 2](/home/tieudaodaide/Documents/Viettel/HW8-DevSecOps-CI-CD/report_img/Pasted image 20240517120310.png)
- While user experience the web site, they can relize that user can access images which are rendered in client by access image in `/upload/`

![[Pasted image 20240517120706.png]](report_img/Pasted image 20240517120706.png)

- Access `shell.php` in web server to confirm the vulnerability

![[Pasted image 20240517120823.png]](report_img/Pasted image 20240517120823.png)

- To fix this vulnerability, i recommend a snippset of code to filter some params of file upload

```PHP
$allowed = [ 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif' ];

// Validate file name 
if (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif)$/', $fileName)) {
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
} 

// Validate file extension 
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); 
if (!array_key_exists($ext, $allowed)) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}

// Validate Content-Type type provided by the browser 
if ($fileType !== $allowed[$ext]) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}

// Validate MIME type using finfo
$finfo = finfo_open(FILEINFO_MIME_TYPE); 
$realMimeType = finfo_file($finfo, $fileTempName); 
finfo_close($finfo); 

if ($realMimeType !== $allowed[$ext]) { 
	header("Location: /edit.php?id=$id&error=invalid_file"); 
	exit; 
}
```

## Insecure Direct Object Reference (/edit.php)

![[Pasted image 20240517231731.png]](report_img/Pasted image 20240517231731.png)

- Server will get the data of jeager to edit by if while dont check that user own it or not. In database design, jaeger 1, 2 and 3 belong to admin

![img](report_img/Pasted image 20240517231943.png)

- Login with user1 credential

![[Pasted image 20240517232209.png]](report_img/Pasted image 20240517232209.png)

- Go to Provide, with user1's credential we can only get data of jaeger 4 & 5

![[Pasted image 20240517232346.png]](report_img/Pasted image 20240517232346.png)

- Try to edit Jeager 5

![[Pasted image 20240517232510.png]](report_img/Pasted image 20240517232510.png)

- Change param id to 1, we can access admin's jaeger

![[Pasted image 20240517232542.png]]()

- To fix this vulnerability, i recommend a snippset of code to check user of jaeger id

```PHP
$cookieValue = base64_decode($_COOKIE['auth']);
list($username, $password) = explode(':', $cookieValue);

$query = "SELECT id FROM users WHERE username = ? AND user_password = ?";
$prepare_query = $conn->prepare($query);
$prepare_query->bind_param("ss", $username, $password);
$prepare_query->execute();
$result = $prepare_query->get_result();
$row = $result->fetch_assoc();

$jaeger = "SELECT * FROM jaegers WHERE id = ? AND user_id = ?";
```
