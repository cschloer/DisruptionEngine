#!/usr/bin/env python
from __future__ import division
import sys
sys.stderr = sys.stdout
import os
sys.path.append(os.getcwd() + '/python_modules/sympy-0.7.6.1');

from sympy import *
from sympy.parsing.sympy_parser import parse_expr
import random
import json

def main():
    arg_list = None
    use_args = False
    if (len(sys.argv) > 1):
        arg_list = iter(sys.argv)
        # iterate past the file name
        arg_list.next()
        use_args = true
    else:
        print "Running this directly on server, not being called by javascript"

    slider_size = 150
    slider_start = 50
    # every 'slider_step' values are calculated. Set to 1 for every value, 10 for every 10 values
    slider_step = 20


    if use_args:
        slider_size = int(arg_list.next())
    if use_args:
        slider_start = int(arg_list.next())
    if use_args:
        slider_step = int(arg_list.next())

    ##
    # values from google glass study
    ##

    # size of mainstream market segment
    S1 = 150000
    # marginal cost of production for new technology firm 1
    cN1 = .210
    # utility from consumption of established technology for mainstream consumers
    uE1 = 1.052
    # Marginal cost of production for established technology firm i
    cEi = .21819
    # number of new technology firms
    nN = 1
    # number of established technology firms
    nE = 4

    if use_args:
        S1 = int(arg_list.next())
    if use_args:
        cN1 = float(arg_list.next())
    if use_args:
        uE1 = float(arg_list.next())
    if use_args:
        cEi = float(arg_list.next())
    if use_args:
        nN = int(arg_list.next())
    if use_args:
        nE = int(arg_list.next())

    eq1LHS = "" + \
        "( (S1 + S2) * (nN / ( 1 + nN) ) ) * " + \
        "( " + \
            "(S1 * (uN1) + S2 * uN2) / (S1 + S2) - " + \
            " cN1 - " + \
            "((nE / (1 + nE)) * S1 * (uE1 - cEi) ) / (S1 + S2) " + \
        ")"
    if use_args:
        eq1LHS = arg_list.next()

    eq1RHS = "" + \
        "S2 * " + \
        "( "+ \
            "(nE / (1 + nE)) * (uE1 - cEi) - " + \
            "(uN1 - uN2)" + \
        ")"
    if use_args:
        eq1RHS = arg_list.next()

    eq1 = eq1LHS + "- (" + eq1RHS + ")"
    eq1_sym = ">="
    if use_args:
        eq1_sym = arg_list.next()
    var_name_list = ['S1', 'cN1', 'uE1', 'cEi', 'nN', 'nE']
    var_list = [S1, cN1, uE1, cEi, nN, nE]
    eq1 = replace_variables(eq1, var_name_list, var_list)
    empty_string = replace_string(eq1)
    # if empty_string is not empty
    if empty_string:
        print 'error: invalid character in equation'
        return

    eq2LHS = "uN2"
    if use_args:
        eq2LHS = arg_list.next()
    eq2RHS = "10"
    if use_args:
        eq2RHS = arg_list.next()
    eq2 = eq2LHS + " - (" + eq2RHS + ")"
    eq2 = replace_variables(eq2, var_name_list, var_list)
    eq2_sym = "<"
    if use_args:
        eq2_sym = arg_list.next()

    eq3LHS = "uN1"
    if use_args:
        eq3LHS = arg_list.next()
    eq3RHS = "uE1"
    if use_args:
        eq3RHS = arg_list.next()
    eq3 = eq3LHS + " - (" + eq3RHS + ")"
    eq3 = replace_variables(eq3, var_name_list, var_list)
    eq3_sym = "<="
    if use_args:
        eq3_sym = arg_list.next()

    eq4LHS = "uN2"
    if use_args:
        eq4LHS = arg_list.next()
    eq4RHS = "uN1"
    if use_args:
        eq4RHS = arg_list.next()
    eq4 = eq4LHS + " - (" + eq4RHS + ")"
    eq4 = replace_variables(eq4, var_name_list, var_list)
    eq4_sym = ">="
    if use_args:
        eq4_sym = arg_list.next()


    s2_eq_list = []
    no_s2_eq_list = []
    # Loop through all of the equations, finding ones that have S2
    for eq in [eq1, eq2, eq3, eq4]:
        if "S2" in eq:
            s2_eq_list.append(eq)
        else:
            no_s2_eq_list.append(eq)


    uN1Sym = Symbol("uN1")
    uN2Sym = Symbol("uN2")

    no_s2_line_list = []

    for eq in no_s2_eq_list:
        # if both variables are present, solve for one and then the other
        if ("uN1" in eq and "uN2" in eq):
            solved_eq = solve(eq, uN2Sym)[0]
            p1 = Point(solve(solved_eq, uN1Sym)[0], 0)
            p2 = Point(solve(solved_eq.__add__(-5), uN1Sym)[0], 5)
            no_s2_line_list.append(Line(p1, p2))
        # if just uN1 is present, just solve for uN1
        elif ("uN1" in eq):
            solved_eq = solve(eq, uN1Sym)[0]
            p1 = Point(solved_eq, 0)
            p2 = Point(solved_eq, 5)
            no_s2_line_list.append(Line(p1, p2))
        # if just uN2 is present, just solve for uN2
        elif ("uN2" in eq):
            solved_eq = solve(eq, uN2Sym)[0]
            p1 = Point(0, solved_eq)
            p2 = Point(5, solved_eq)
            no_s2_line_list.append(Line(p1, p2))


    vertice_list = []
    line_list_copy = list(no_s2_line_list)

    for line1 in no_s2_line_list:
        line_list_copy.remove(line1)
        for line2 in line_list_copy:
            vertice_list.append(intersection(line1, line2)[0])




    graph_vals1 = []

    for S2 in range(slider_start, slider_start + slider_size + 1):
        # We still want something appended to the list so it can synch up correctly in terms of indices
        if (S2 - slider_start) % slider_step is not 0:
            graph_vals1.append(None)
            continue

        cur_vertice_list = list(vertice_list)

        s2_line_list = []
        # Loop through all of the equations that have S2 in them
        for eq in s2_eq_list:
            eq_new = eq.replace("S2", str(S2))
            # both variables present, solve that way
            if ("uN1" in eq_new and "uN2" in eq_new):
                solved_eq = solve(eq_new, uN2Sym)[0]
                p1 = Point(solve(solved_eq, uN1Sym)[0], 0)
                p2 = Point(solve(solved_eq.__add__(-5), uN1Sym)[0], 5)
                s2_line_list.append(Line(p1, p2))
            # only uN1 present
            elif ("uN1" in eq_new):
                solved_eq = solve(eq_new, uN1Sym)[0]
                p1 = Point(solved_eq, 0)
                p2 = Point(solved_eq, 5)
                s2_line_list.append(Line(p1, p2))
            # only uN2 present
            elif ("uN2" in eq_new):
                solved_eq = solve(eq_new, uN2Sym)[0]
                p1 = Point(0, solved_eq)
                p2 = Point(5, solved_eq)
                s2_line_list.append(Line(p1, p2))

        line_list_copy = list(s2_line_list) + list(no_s2_line_list)
        for line1 in s2_line_list:
            line_list_copy.remove(line1)
            for line2 in line_list_copy:
                cur_vertice_list.append(intersection(line1, line2)[0])

        final_vertice_list = []
        for vertice in cur_vertice_list:
            if vertice_in_bounds(S2, vertice, [eq1, eq2, eq3, eq4], [eq1_sym, eq2_sym, eq3_sym, eq4_sym]):
                final_vertice_list.append(vertice)

        unordered_quad = []
        for vertice in final_vertice_list:
            x = vertice.x.p / vertice.x.q
            y = vertice.y.p / vertice.y.q
            unordered_quad.append([x, y])

        new_quad = []
        if len(unordered_quad) == 4:
            # Now determine the order of the vertices
            left_vertices = two_smallest(unordered_quad)
            left_bottom_vertex = one_smallest(left_vertices, 1)
            left_top_vertex = left_vertices[0]
            # right vertices are left over in unordered_quad object
            right_bottom_vertex = one_smallest(unordered_quad, 1)
            right_top_vertex = unordered_quad[0]
            new_quad = [left_bottom_vertex, left_top_vertex, right_top_vertex, right_bottom_vertex]
        else:
            new_quad = unordered_quad

        graph_vals1.append(new_quad)

    print json.dumps(graph_vals1)

# Get the two smallest x values, deleting them frm the list in the process
def two_smallest(vertex_list):
    vertex1 = one_smallest(vertex_list, 0)
    vertex2 = one_smallest(vertex_list, 0)
    return [vertex1, vertex2]

# Get the vertice with the smallest n index (0 for x, 1 for y)
def one_smallest(vertex_list, n):
    min_val = vertex_list[0][n]
    min_index = 0;
    for i in range(1, len(vertex_list)):
        vertex = vertex_list[i]
        if (vertex[n] < min_val):
            min_val = vertex[n]
            min_index = i

    return vertex_list.pop(min_index)

def vertice_in_bounds(S2, vertice, eq_list, eq_sym_list):
    x = vertice.x.p / vertice.x.q
    y = vertice.y.p / vertice.y.q
    for (eq, eq_sym) in zip(eq_list, eq_sym_list):
        eq = eq.replace('S2', str(S2))
        eq = eq.replace("uN1", str(x))
        eq = eq.replace("uN2", str(y))
        result = round(float(parse_expr(eq)), 4)
        # Combining '>=' and '>' -- their difference doesn't make sense for creating shapes
        if eq_sym in ['>=', '>']:
            if result < 0:
                return False
        else:
            if result > 0:
                return False

    return True

def replace_variables(eq, var_name_list, var_list):
    for (var_name, var) in zip(var_name_list, var_list):
        eq = eq.replace(var_name, str(var))
    return eq

def replace_string(eq):
    eq = eq.replace('S2', '')
    eq = ''.join([i for i in eq if not i.isdigit()])
    return ''

main()

