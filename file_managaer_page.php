<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>file managaer page - Bootdey.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<link href="https://cdn.lineicons.com/3.0/lineicons.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />

<div class="container">
<div class="row">
    <div class="col-12 col-lg-3">
		<div class="card">
			<div class="card-body">
				<div class="d-grid"> <a href="javascript:;" class="btn btn-primary">+ Add File</a>
				</div>
				<h5 class="my-3">My Drive</h5>
				<div class="fm-menu">
					<div class="list-group list-group-flush"> <a href="javascript:;" class="list-group-item py-1"><i class="bx bx-folder me-2"></i><span>All Files</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-devices me-2"></i><span>My Devices</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-analyse me-2"></i><span>Recents</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-plug me-2"></i><span>Important</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-trash-alt me-2"></i><span>Deleted Files</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-file me-2"></i>
                    <span>Documents</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-image me-2"></i><span>Images</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-video me-2"></i><span>Videos</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-music me-2"></i><span>Audio</span></a>
						<a href="javascript:;" class="list-group-item py-1"><i class="bx bx-beer me-2"></i><span>Zip Files</span></a>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-body">
				<h5 class="mb-0 text-primary font-weight-bold">45.5 GB <span class="float-end text-secondary">50 GB</span></h5>
				<p class="mb-0 mt-2"><span class="text-secondary">Used</span><span class="float-end text-primary">Upgrade</span>
				</p>
				<div class="progress mt-3" style="height:7px;">
					<div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
					<div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
					<div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<div class="mt-3"></div>
				<div class="d-flex align-items-center">
					<div class="fm-file-box bg-light-primary text-primary"><i class="bx bx-image"></i>
					</div>
					<div class="flex-grow-1 ms-2">
						<h6 class="mb-0">Images</h6>
						<p class="mb-0 text-secondary">1,756 files</p>
					</div>
					<h6 class="text-primary mb-0">15.3 GB</h6>
				</div>
				<div class="d-flex align-items-center mt-3">
					<div class="fm-file-box bg-light-success text-success"><i class="bx bxs-file-doc"></i>
					</div>
					<div class="flex-grow-1 ms-2">
						<h6 class="mb-0">Documents</h6>
						<p class="mb-0 text-secondary">123 files</p>
					</div>
					<h6 class="text-primary mb-0">256 MB</h6>
				</div>
				<div class="d-flex align-items-center mt-3">
					<div class="fm-file-box bg-light-danger text-danger"><i class="bx bx-video"></i>
					</div>
					<div class="flex-grow-1 ms-2">
						<h6 class="mb-0">Media Files</h6>
						<p class="mb-0 text-secondary">24 files</p>
					</div>
					<h6 class="text-primary mb-0">3.4 GB</h6>
				</div>
				<div class="d-flex align-items-center mt-3">
					<div class="fm-file-box bg-light-warning text-warning"><i class="bx bx-image"></i>
					</div>
					<div class="flex-grow-1 ms-2">
						<h6 class="mb-0">Other Files</h6>
						<p class="mb-0 text-secondary">458 files</p>
					</div>
					<h6 class="text-primary mb-0">3 GB</h6>
				</div>
				<div class="d-flex align-items-center mt-3">
					<div class="fm-file-box bg-light-info text-info"><i class="bx bx-image"></i>
					</div>
					<div class="flex-grow-1 ms-2">
						<h6 class="mb-0">Unknown Files</h6>
						<p class="mb-0 text-secondary">57 files</p>
					</div>
					<h6 class="text-primary mb-0">178 GB</h6>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-9">
		<div class="card">
			<div class="card-body">
				<div class="fm-search">
					<div class="mb-0">
						<div class="input-group input-group-lg">	<span class="input-group-text bg-transparent"><i class="fa fa-search"></i></span>
							<input type="text" class="form-control" placeholder="Search the files">
						</div>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="fm-icon-box radius-15 bg-primary text-white"><i class="lni lni-google-drive"></i>
									</div>
									<div class="ms-auto font-24"><i class="fa fa-ellipsis-h"></i>
									</div>
								</div>
								<h5 class="mt-3 mb-0">Google Drive</h5>
								<p class="mb-1 mt-4"><span>45.5 GB</span>  <span class="float-end">50 GB</span>
								</p>
								<div class="progress" style="height: 7px;">
									<div class="progress-bar bg-primary" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="fm-icon-box radius-15 bg-danger text-white"><i class="lni lni-dropbox-original"></i>
									</div>
									<div class="ms-auto font-24"><i class="fa fa-ellipsis-h"></i>
									</div>
								</div>
								<h5 class="mt-3 mb-0">Dropbox</h5>
								<p class="mb-1 mt-4"><span>1,2 GB</span>  <span class="float-end">3 GB</span>
								</p>
								<div class="progress" style="height: 7px;">
									<div class="progress-bar bg-danger" role="progressbar" style="width: 45%;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="fm-icon-box radius-15 bg-warning text-dark"><i class="bx bxs-door-open"></i>
									</div>
									<div class="ms-auto font-24"><i class="fa fa-ellipsis-h"></i>
									</div>
								</div>
								<h5 class="mt-3 mb-0">OneDrive</h5>
								<p class="mb-1 mt-4"><span>2,5 GB</span>  <span class="float-end">3 GB</span>
								</p>
								<div class="progress" style="height: 7px;">
									<div class="progress-bar bg-warning" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end row-->
				<h5>Folders</h5>
				<div class="row mt-3">
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="font-30 text-primary"><i class="bx bxs-folder"></i>
									</div>
									<div class="user-groups ms-auto">
										<img src="https://bootdey.com/img/Content/avatar/avatar1.png" width="35" height="35" class="rounded-circle" alt="">
										<img src="https://bootdey.com/img/Content/avatar/avatar2.png" width="35" height="35" class="rounded-circle" alt="">
									</div>
									<div class="user-plus">+</div>
								</div>
								<h6 class="mb-0 text-primary">Analytics</h6>
								<small>15 files</small>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="font-30 text-primary"><i class="bx bxs-folder"></i>
									</div>
									<div class="user-groups ms-auto">
										<img src="https://bootdey.com/img/Content/avatar/avatar7.png" width="35" height="35" class="rounded-circle" alt="">
									</div>
								</div>
								<h6 class="mb-0 text-primary">Assets</h6>
								<small>345 files</small>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4">
						<div class="card shadow-none border radius-15">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div class="font-30 text-primary"><i class="bx bxs-folder"></i>
									</div>
									<div class="user-groups ms-auto">
										<img src="https://bootdey.com/img/Content/avatar/avatar2.png" width="35" height="35" class="rounded-circle" alt="">
										<img src="https://bootdey.com/img/Content/avatar/avatar3.png" width="35" height="35" class="rounded-circle" alt="">
									</div>
								</div>
								<h6 class="mb-0 text-primary">Marketing</h6>
								<small>143 files</small>
							</div>
						</div>
					</div>
				</div>
				<!--end row-->
				<div class="d-flex align-items-center">
					<div>
						<h5 class="mb-0">Recent Files</h5>
					</div>
					<div class="ms-auto"><a href="javascript:;" class="btn btn-sm btn-outline-secondary">View all</a>
					</div>
				</div>
				<div class="table-responsive mt-3">
					<table class="table table-striped table-hover table-sm mb-0">
						<thead>
							<tr>
								<th>Name <i class="bx bx-up-arrow-alt ms-2"></i>
								</th>
								<th>Members</th>
								<th>Last Modified</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file-pdf me-2 font-24 text-danger"></i>
										</div>
										<div class="font-weight-bold text-danger">Competitor Analysis Template</div>
									</div>
								</td>
								<td>Only you</td>
								<td>Sep 3, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file me-2 font-24 text-primary"></i>
										</div>
										<div class="font-weight-bold text-primary">How to Create a Case Study</div>
									</div>
								</td>
								<td>3 members</td>
								<td>Jun 12, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file me-2 font-24 text-primary"></i>
										</div>
										<div class="font-weight-bold text-primary">Landing Page Structure</div>
									</div>
								</td>
								<td>10 members</td>
								<td>Jul 17, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file-pdf me-2 font-24 text-danger"></i>
										</div>
										<div class="font-weight-bold text-danger">Meeting Report</div>
									</div>
								</td>
								<td>5 members</td>
								<td>Aug 28, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file me-2 font-24 text-primary"></i>
										</div>
										<div class="font-weight-bold text-primary">Project Documents</div>
									</div>
								</td>
								<td>Only you</td>
								<td>Aug 17, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file-doc me-2 font-24 text-success"></i>
										</div>
										<div class="font-weight-bold text-success">Review Checklist Template</div>
									</div>
								</td>
								<td>7 members</td>
								<td>Sep 8, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file me-2 font-24 text-primary"></i>
										</div>
										<div class="font-weight-bold text-primary">How to Create a Case Study</div>
									</div>
								</td>
								<td>3 members</td>
								<td>Jun 12, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file me-2 font-24 text-primary"></i>
										</div>
										<div class="font-weight-bold text-primary">Landing Page Structure</div>
									</div>
								</td>
								<td>10 members</td>
								<td>Jul 17, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center">
										<div><i class="bx bxs-file-doc me-2 font-24 text-success"></i>
										</div>
										<div class="font-weight-bold text-success">Review Checklist Template</div>
									</div>
								</td>
								<td>7 members</td>
								<td>Sep 8, 2019</td>
								<td><i class="fa fa-ellipsis-h font-24"></i>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<style type="text/css">
body{
margin-top:20px;
background-color: #f7f7ff;
}
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0px solid rgba(0, 0, 0, 0);
    border-radius: .25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 6px 0 rgb(218 218 253 / 65%), 0 2px 6px 0 rgb(206 206 238 / 54%);
}
.fm-file-box {
    font-size: 25px;
    background: #e9ecef;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .25rem;
}
.ms-2 {
    margin-left: .5rem!important;
}

.fm-menu .list-group a {
    font-size: 16px;
    color: #5f5f5f;
    display: flex;
    align-items: center;
}
.list-group-flush>.list-group-item {
    border-width: 0 0 1px;
}
.list-group-item+.list-group-item {
    border-top-width: 0;
}
.py-1 {
    padding-top: .25rem!important;
    padding-bottom: .25rem!important;
}
.list-group-item {
    position: relative;
    display: block;
    padding: .5rem 1rem;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, .125);
}

.radius-15 {
    border-radius: 15px;
}
.fm-icon-box {
    font-size: 32px;
    background: #ffffff;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .25rem;
}
.font-24 {
    font-size: 24px;
}
.ms-auto {
    margin-left: auto!important;
}
.font-30 {
    font-size: 30px;
}
.user-groups img {
    margin-left: -14px;
    border: 1px solid #e4e4e4;
    padding: 2px;
    cursor: pointer;
}

.rounded-circle {
    border-radius: 50%!important;
}
</style>

<script type="text/javascript">

</script>
</body>
</html>