# Create your views here.
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse

import simplejson

@login_required
def trust_external(request):
    u = request.user
    result = {
        'username': u.username,
        'userfullname': "%s %s" % (u.first_name, u.last_name),
        'useremail': user.email,
        'grps': [ g.name for g in u.groups.all() ]
    }
    response = HttpResponse(simplejson.dumps(result))
    response['Content-Type'] = 'application/json'
    return response

def retrieve_groups(request):
    result = [ g.name for g in Group.objects.all() ]
    response = HttpResponse(simplejson.dumps(result))
    response['Content-Type'] = 'application/json'
    return response

