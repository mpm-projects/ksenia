<?php
/**
 * KSENIA PORTFOLIO
 * @copyright 2014 mparaiso <mparaiso@online.fr>
 * all rights reserved
 * this code was open sourced for educational purpose only.
 */
$templates = array();

$templates['layout'] = <<<HERE
	<!doctype html>
	<html lang='{{app.locale}}'>
		<head>
			<title>{{app.title}}</title>
			{% block metas %}
				<meta charset='UTF-8' />
				<meta http-equiv='X-UA-Compatible' content='IE=edge'/>
				<meta name='viewport' content='width=device-width, initial-scale=1'/>
			{% endblock %}
			{% block styles %}{% endblock %}
		</head>
		<body>
			<main class='container'>
				<noscript><h2 class="alert alert-warning">Please Enable Javascript!</h2></noscript>
				<h1>{{app.title}}</h1>
				{%block content%}{%endblock%}
			</main>
			{% block scripts %}
			{% endblock %}
		</body>
	</html>
HERE;

$templates['index'] = <<<HERE
	{% extends 'layout' %}
	{% block content %}
		<h1>HOMEPAGE</h1>
	{% endblock %}
HERE;

/** administration */
$templates['admin_layout'] = <<<HERE
	{% extends 'layout'%}
	{% block styles %}
		{# Latest compiled and minified CSS #}
		<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css'>
		{# Optional theme #}
		<link rel='stylesheet' href='//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css'>
		{# google font #}
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>
		{# custom styles #}
		<link rel="stylesheet" href="/static/css/styles.css">
	{% endblock %}
	{% block content %}
		<section class='row'>
		<aside class='col-md-3'>
		{%include 'admin_nav'%}
		</aside>
		<article class='col-md-9'>
			{% block admin_content %}
			{% endblock %}
		</article>
		</section>
	{% endblock %}
	{% block scripts %}
		{# jQuery (necessary for Bootstrap's JavaScript plugins) #}
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
		{# Latest compiled and minified JavaScript #}
		<script src='//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js'></script>
	{% endblock %}
HERE;

$templates['admin_nav'] = <<<HERE
	<ul class="list-group">
		<li class="list-group-item"><strong><a href="{{path('admin_index')}}">DASHBOARD</a></strong></li>
		<li class="list-group-item text-muted uppercase">
			<strong>PROJECTS</strong>
		</li>
		<li class="list-group-item"><a href="{{path('project_index')}}">Manage Projects</a></li>
		<li class="list-group-item"><a href="{{path('project_new')}}">Create a new project</a></li>
	</ul>
HERE;

$templates['admin_index'] = <<<HERE
	{% extends 'admin_layout' %}
	{% block admin_content %}
        <header class="lead text-muted">ADMINISTRATION</header>
	{% endblock %}
HERE;

$templates['admin_upload'] = <<<HERE
	{% extends 'admin_layout' %}
	{% block content %}
	<h2>Upload</h2>
	{% endblock %}
HERE;

/** Project management */

/* list all projects */
$templates['project_index'] = <<<HERE
	{% extends 'admin_layout' %}
	{% block admin_content %}
		<h2>Projects</h2>
		{% if projects and projects|length > 0 %}
		<table class="table">
				<thead>
				<tr>
					<th>Title</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{% for project in projects %}
				<tr>
					<td><a href="{{ path('project_read',{id:project.id}) }}">{{project.title}}</a></td>
					<td>{{project.description[:50]~"..."}}</td>
					<td><a href="{{path('image_index',{projectId:project.id}) }}">Manage Images</a></td>
					<td>
						<a href="{{ path('project_update',{id:project.id}) }}">Edit</a>
						<a href="{{ path('project_delete',{id:project.id}) }}">Remove</a>
					</td>
				</tr>
				{% endfor %}
			</tbody>
		</table>
		{% else %}
			<h3 class="text-warning">No Project found</h3>
			<p><a href="{{path('project_new')}}">Create a project</a></p>
		{% endif %}
	{% endblock%}
HERE;


$templates['project_form'] = <<<HERE
		{# @node @silex display a Symfony form by field #}
		{{form_start(form)}}
		{{ form_errors(form) }}
		{% for field in form %}
		<div class="form-group">
		        {{form_row(field,{attr:{class:'form-control'}})}}
		 </div>
		{% endfor %}
		<button type="reset" class="btn btn-default">Reset</button>
		<button type="submit" class="btn btn-default">Create</button>
		{{form_end(form)}}
HERE;

/* create a new project */
$templates['project_new'] = <<<HERE
	{%extends 'admin_layout' %}
	{% block admin_content %}
		<h2>NEW PROJECT</h2>
		<p class='text-muted'>You'll be able to add images after the project is saved</p>
		{% include 'project_form' with {form:form} %}
	{% endblock %}
	{% block scripts %}
	{{ parent() }}
	{% endblock %}
HERE;

$templates['project_update'] = <<<HERE
	{%extends 'admin_layout' %}
	{% block admin_content %}
		<h2>EDIT PROJECT</h2>
		<p>{{project.title}}</p>
		<a class="btn btn-default" href="{{path('image_create',{projectId:project.id})}}">
		Add a new image</a>
		{% include 'project_form' with {form:form} %}
	{% endblock %}
	{% block scripts %}
	{{ parent() }}
	<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
	<script src="/static/javascript/jquery-plugins.js"></script>
	{#<script src="/static/javascript/project-new.js"></script>#}
	{% endblock %}
HERE;

$templates['project_read'] = <<<HERE
	{%extends 'admin_layout' %}
	{% block admin_content %}
		<header class="lead">
            <ol class="breadcrumb">
                <li class="active">{{project.title}}</li>
            </ol>
        </header>
		<dl class="dl-horizontal">
		    <dt>Title</dt>
		    <dd>{{project.title}}</dd>
            <dt>Description</dt>
            <dd>{{project.description}}</dd>
            <dt>Client</dt>
            <dd>{{project.client}}</dd>
            <dt>Tags</dt>
            <dd>{{ project.tags | join(', ')}}</dd>
		</dl>
		<p class="row">
			<a class="btn btn-default" href="{{path('project_update',{id:project.id})}}">Edit</a>
		    <a class="btn btn-default" href="{{path('image_index',{projectId:project.id})}}">Manage Images</a>
		    <a class="btn btn-default" href="{{path('image_create',{projectId:project.id})}}">Add a new Image</a>
		</p>
		{% for row in project.images|batch(5)%}
	    <p class="row">
		{% for image in row %}
		<div class="col-md-2 thumbnail">
		    <a href="{{path('image_read',{projectId:project.id,imageId:image.id}) }}">
		        <img height="150" src="{{path('image_load',{imageId:image.id}) }}" alt="{{image.title}}"/>
		    </a>
		    <h4 class="text-muted"><small>{{image.title}}</small></h4>
		    <small><a class="btn btn-default btn-xs" href="{{path('image_update',{projectId:project.id,imageId:image.id}) }}">Edit</a></small>
		    <form class="inline" method="POST" action="{{path('image_delete',{projectId:project.id,imageId:image.id}) }}">
		        <input type="hidden" name="_method" value="DELETE"/>
		        <button type="submit" class="btn btn-default btn-xs">Remove</button>
		    </form>

		</div>
		{%endfor%}
		</p>
		{%endfor%}
	{% endblock %}
	{% block scripts %}
	{{ parent() }}
	{% endblock %}
HERE;

/**
 * IMAGES
 */

$templates['image_index'] = <<<HERE
    {%extends 'admin_layout'%}
    {%block admin_content%}
    	<header class="lead">
            <ol class="breadcrumb">
                <li><a href="{{path('project_read',{id:project.id}) }}">{{project.title}}</a></li>
                <li class="active">Images from project {{project.title}}</li>
            </ol>
        </header>
        <a class="btn btn-default" href="{{path('image_create',{projectId:project.id})}}">Add New</a>
        {%if  project.images|length>0%}
        <table class="table">
            <thead>
                <tr>
                <td></td>
                <td>Title</td>
                <td>Description</td>
                <td></td>
                </tr>
            </thead>
            <tbody>
            {%for image in project.images%}
            <tr>
                <td>
                    <a href="{{path('image_read',{projectId:project.id,imageId:image.id})}}">
                        <img width="100" src="{{path('image_load',{imageId:image.id}) }}" alt="{{image.title}}"/>
                    </a>
                <td>{{image.title}}</td>
                <td>{{image.description}}</td>
                <td>
                <a class="btn btn-link" href="{{path('image_update',{projectId:project.id,imageId:image.id}) }}">Edit</a>
                <form class="inline"
                action="{{path('image_delete',{projectId:project.id,imageId:image.id}) }}"
                method="POST">
                    <input type="hidden" id="_method" name="_method" value="DELETE" />
                    <button class="btn btn-link" type="submit">Remove</button>
                </form>
                </td>
            </tr>
            {%endfor%}
            </tbody>
        </table>
        {%else%}
        <p>No image yet</p>
        {%endif%}

    {%endblock%}
HERE;

$templates['image_read'] = <<<HERE
    {% extends 'admin_layout'%}
    {%block admin_content%}
    <header class="lead">
        <ol class="breadcrumb">
            <li><a href="{{path('project_read',{id:project.id}) }}">{{project.title}}</a></li>
            <li class="active">{{image.title}}</li>
        </ol>
    </header>
    <div class="row">
        <div class="thumbnail">
            <img src="{{path('image_load',{imageId:image.id}) }}" alt="{{image.title}}"/>
        </div>
    </div>
    <dl class="dl-horizontal">
        <dt>title</dt>
        <dd>{{image.title}}</dd>
        <dt>description</dt>
        <dd>{{image.description}}</dd>
    </dl>
    {%endblock%}
HERE;

$templates['image_form'] = <<<HERE
    {{form_start(form)}}
    {% for field in form %}
    <div class="form-group">
        {{ form_row(field,{attr:{class:'form-control'}}) }}
    </div>
    {% endfor %}
    <button type="reset">Reset</button>
    <button type="submit">Submit</button>
    {{form_end(form)}}
HERE;


$templates['image_create'] = <<<HERE
	{%extends 'admin_layout'%}
	{%block admin_content %}
		<header class="lead">Add a new image to "<em>{{project.title}}</em> "</header>
        {%include 'image_form' with {form:form}%}
	{%endblock%}
HERE;

$templates['image_update'] = <<<HERE
	{%extends 'admin_layout'%}
	{%block admin_content %}
		<header class="lead">Update image for project <a href="{{path('project_read',{id:project.id}) }}">{{project.title}}</a></header>
		<div class="row">
		<a class="thumbnail col-md-5"
          target="_blank" href="{{path('image_load',{imageId:image.id})}}">
            <img title="{{image.title}}"
            src="{{path('image_load',{imageId:image.id})}}"
            alt="{{image.title}}"/>
        </a>
        </div>
        <hr>
        {%include 'image_form' with {form:form}%}
	{%endblock%}
HERE;


return $templates;