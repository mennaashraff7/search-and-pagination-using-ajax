<?php
 //connection to db
$connect = new PDO("mysql:host=localhost; dbname=college", "root", "");
    $search_value = $_POST['query'];  //search input value
    $limit = 5;                       // limit of courses in one page
    $page = 1;
    $output ='';
    if($_POST["page"]>1)
    {
        $start = (($_POST["page"]-1) * $limit);
        $page = $_POST["page"];

    }else{
        $start = 0;
    }
    
    $query = "SELECT course_name,course_description,professor_name,department_name
              FROM course 
              INNER JOIN department ON department.department_id = course.department_id
              INNER JOIN professor ON professor.professor_id = course.professor_id
              ";
            
            if( $search_value != '')
            {
              $query .= "WHERE REPLACE(course_name,' ','') LIKE REPLACE('"."%".$search_value."%"."',' ','') 
              ||  REPLACE(course_description,' ','') LIKE REPLACE('"."%".$search_value."%"."',' ','') 
              ||  REPLACE(department_name,' ','') LIKE REPLACE('"."%".$search_value."%"."',' ','') 
              ||  REPLACE(professor_name,' ','') LIKE REPLACE('"."%".$search_value."%"."',' ','')
              ||  REPLACE(professor_name,'nn','n') LIKE REPLACE('"."%".$search_value."%"."','nn','n')
              ||  REPLACE(course_name,'mm','m') LIKE REPLACE('"."%".$search_value."%"."','mm','m')
              ||  REPLACE(professor_name,'mm','m') LIKE REPLACE('"."%".$search_value."%"."','mm','m')";
            }
            
              
            $query .= 'ORDER BY course_name ';    
            $filter_query = $query . 'LIMIT '.$start.', '.$limit.'';

            $statement = $connect->prepare($query);
            $statement->execute();
            $total_data = $statement->rowCount();

            $statement = $connect->prepare($filter_query);
            $statement->execute();
            $result = $statement->fetchAll();
            $total_filter_data = $statement->rowCount();

$output = '
<label>Total Courses : '.$total_data.'</label>
<table class="table table-striped table-hover">
  <tr>
    <th> Course Name</th>
    <th>professor Name</th>
    <th> department Name</th>
    <th>Course description</th>
  </tr>
';
if($total_data > 0)
{
  foreach($result as $row)
  {
    $output .= '
    <tr>
      <td>'.$row["course_name"].'</td>
      <td>'.$row["professor_name"].'</td>
      <td>'.$row["department_name"].'</td>
      <td>'.$row["course_description"].'</td>
    </tr>
    ';
  }
}
else
{
  $output .= '
  <tr>
    <td colspan="4" align="center">No Data Found</td>
  </tr>
  ';
}

$output .= '
</table>
<br />
<div align="center">
  <ul class="pagination">
';

$total_links = ceil($total_data/$limit);
$previous_link = '';
$next_link = '';
$page_link = '';

//echo $total_links;

if($total_links > 2)
{
  if($page < 2)
  {
    for($count = 1; $count <= 2; $count++)
    {
      $page_array[] = $count;
    }
    $page_array[] = '...';
    $page_array[] = $total_links;
  }
  else
  {
    $end_limit = $total_links - 2;
    if($page > $end_limit)
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $end_limit; $count <= $total_links; $count++)
      {
        $page_array[] = $count;
      }
    }
    else
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $page - 1; $count <= $page + 1; $count++)
      {
        $page_array[] = $count;
      }
      $page_array[] = '...';
      $page_array[] = $total_links;
    }
  }
}
else
{
  for($count = 1; $count <= $total_links; $count++)
  {
    $page_array[] = $count;
  }
}
if($total_data >0){
for($count = 0; $count < count($page_array); $count++)
{
  if($page == $page_array[$count])
  {
    $page_link .= '
    <li class="page-item active">
      <a class="page-link" href="#">'.$page_array[$count].'</a>
    </li>
    ';

    $previous_id = $page_array[$count] - 1;
    if($previous_id > 0)
    {
      $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Previous</a></li>';
    }
    else
    {
      $previous_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Previous</a>
      </li>
      ';
    }
    $next_id = $page_array[$count] + 1;
    if($next_id >= $total_links)
    {
      $next_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Next</a>
      </li>
        ';
    }
    else
    {
      $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Next</a></li>';
    }
  }
  else
  {
    if($page_array[$count] == '...')
    {
      $page_link .= '
      <li class="page-item disabled">
          <a class="page-link" href="#">...</a>
      </li>
      ';
    }
    else
    {
      $page_link .= '
      <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$page_array[$count].'">'.$page_array[$count].'</a></li>
      ';
    }
  }
}
}
$output .= $previous_link . $page_link . $next_link;
$output .= '
  </ul>

</div>
';

echo $output;
        

?>