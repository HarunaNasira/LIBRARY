<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper">

        <!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                <a class="text-dark" href="/">Manage Books</a> / Add Books
            </h5>
            <div class="d-inline-flex gap-2">
                <a href="booklist.php" class="mr-2 p-3 text-dark">
                    View All Books
                </a>
                <button class="btn btn-primary p-3 rounded-2">
                    +  Add Book Category
                </button>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="card rounded-3">
                <div class="card-body">
                    <h4 class="card-title">Book Information</h4>

                    <form class="forms-sample">
                        <div class="form-group row">
                            <div class="col">
                                <label>Book Title <span class="text-danger">*</span></label>
                                <input class="form-control" name="bootTitle" required placeholder="In the Chest of a Woman"/>
                            </div>
                            <div class="col">
                                <label>Author(s) <span class="text-danger">*</span></label>
                                <input class="form-control" name="authors" required placeholder="Efo Kodjo Mawugbe"/>
                            </div>
                            <div class="col">
                                <label>ISBN <span class="text-danger">*</span></label>
                                <input class="form-control" name="ISBN" required placeholder="___-____-___-_"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Genre/Category <span class="text-danger">*</span></label>
                                <select class="form-control" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="fiction">Fiction</option>
                                    <option value="non-fiction">Non-Fiction</option>
                                    <option value="science">Science</option>
                                    <option value="technology">Technology</option>
                                    <option value="history">History</option>
                                    <option value="biography">Biography</option>
                                    <option value="business">Business</option>
                                    <option value="education">Education</option>
                                    <option value="literature">Literature</option>
                                    <option value="philosophy">Philosophy</option>
                                    <option value="psychology">Psychology</option>
                                    <option value="sociology">Sociology</option>
                                    <option value="art">Art</option>
                                    <option value="music">Music</option>
                                    <option value="poetry">Poetry</option>
                                    <option value="drama">Drama</option>
                                    <option value="children">Children's Books</option>
                                    <option value="reference">Reference</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Total Copies <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" required />
                            </div>
                            <div class="col">
                                <label>Shelf/Location Code</label>
                                <input class="form-control" name="shelfCode" />
                            </div>
                        </div>
                        <div class="my-3"></div>

                        <h4 class="card-title nav-underline">Book Features</h4>
                        <div class="form-group row">
                            <div class="col">
                                <label>Published Year</label>
                                <input class="form-control" name="pubYear" type="year" placeholder="1980"/>
                            </div>
                            <div class="col">
                                <label>Language <span class="text-danger">*</span></label>
                                <select class="form-control" name="language" required>
                                    <option value="">Select Language</option>
                                    <option value="english">English</option>
                                    <option value="french">French</option>
                                    <option value="spanish">Spanish</option>
                                    <option value="german">German</option>
                                    <option value="italian">Italian</option>
                                    <option value="portuguese">Portuguese</option>
                                    <option value="russian">Russian</option>
                                    <option value="chinese">Chinese</option>
                                    <option value="japanese">Japanese</option>
                                    <option value="arabic">Arabic</option>
                                    <option value="hindi">Hindi</option>
                                    <option value="urdu">Urdu</option>
                                    <option value="swahili">Swahili</option>
                                    <option value="yoruba">Yoruba</option>
                                    <option value="hausa">Hausa</option>
                                    <option value="twi">Twi</option>
                                    <option value="ewe">Ewe</option>
                                    <option value="ga">Ga</option>
                                    <option value="dagbani">Dagbani</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="formFile" class="form-label">Upload Book Thumbnail <span class="text-danger">*</span></label>
                                <input class="form-control" type="file" id="formFile" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="floatingTextarea2">Book Summary <span class="text-danger">*</span></label>
                            <textarea class="form-control" placeholder="Add a brief description of the book..." required id="floatingTextarea2" style="height: 100px"></textarea>
                        </div>
                        <button type="submit" name="saveBookDetails" class="btn btn-primary d-flex justify-self-end mt-2 px-5 py-3">Save</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
