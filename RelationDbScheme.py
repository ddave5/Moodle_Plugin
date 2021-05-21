import sys
import networkx as nx
import Graph
import GraphEditDistance as gre
import matplotlib.pyplot as plt
import io
import re


def strBetweenTags(tag, text):
    find = re.findall(r'<' + tag + '>(.*?)<' + tag + '>', text)
    return len(find) == 1


def removeTagsInString(text):
    return re.sub("<.*?>", "", text)


def searchAttrInEgyed(graphid, egyednev, attrnev):
    for egyed in graphid.egyedek:
        if egyed.nev == egyednev:
            for attr in egyed.attributumok:
                if attr.nev == attrnev:
                    return attr
    return None


def textanalyze(txt, graphid, dg):
    global nodeid
    file = io.open(txt, mode="r", encoding="utf-8")
    for line in file:
        line = line.replace(")", "")
        line = line.replace('\n', ' ').replace('\r', '')
        line = line.replace(" ", "")  # szóközök törlése
        line = line.replace("\ufeff", "")  # szóközök törlése
        line = line.replace("*", "<u>")
        line = line.replace("_", "<i>")
        line = line.lower()

        # Séma
        spl = line.split("(")
        egyed = Graph.Sema(nodeid, spl[0], "")
        dg.add_nodes_from([(nodeid, {'label': egyed.nev, 'object': egyed})])
        nodeid = nodeid + 1

        # Attribútumok
        for attr in spl[1].split(","):
            kulcs = strBetweenTags('u', attr)  # ha <u> tagok között van
            gyengekulcs = strBetweenTags('i', attr)  # ha <i> tagok között van
            nev = removeTagsInString(attr)

            if len(nev.split(".")) > 1:
                egyed.hivatkozik = egyed.hivatkozik + nev + "-"
            else:
                a = Graph.RelaciosAttributum(nodeid, nev, kulcs, gyengekulcs)
                dg.add_nodes_from([(nodeid, {'label': a.nev, 'object': a})])
                nodeid = nodeid + 1
                egyed.ujAttributum(a)
                # print(egyed.nev, "ujAttributum", a)
                dg.add_edges_from([(egyed.id, a.id, {'e': egyed.nev + '-' + a.nev, 'd': True})])  # irányított, ezért fontos a sorrend
                graphid.ujAttributum(a)

        graphid.ujEgyed(egyed)

        # Kapcsolatok
        for egyed in graphid.egyedek:
            if egyed.hivatkozik:
                spl = egyed.hivatkozik.split("-")
                honnan = searchAttrInEgyed(graphid, spl[0].split('.')[0], spl[0].split('.')[1])
                hova = searchAttrInEgyed(graphid, spl[1].split('.')[0], spl[1].split('.')[1])
                dg.add_edges_from([(honnan.id, hova.id, {'e': honnan.nev + '-' + hova.nev, 'd': True})])  # irányított, ezért fontos a sorrend

                dg.remove_nodes_from([egyed.id])
                graphid.egyedek.remove(egyed)


def startged():

    alga = gre.graph_edit_distance(d2, d1, node_match=Graph.nodeMatch, edge_match=Graph.edgeMatch,
                                   node_del_cost=Graph.nodeDelCost, node_ins_cost=Graph.nodeInsCost,
                                   edge_del_cost=Graph.edgeDelCost, edge_ins_cost=Graph.edgeInsCost, upper_bound=None)

    maxpoints = int(sys.argv[3]) - alga
    if maxpoints < 0:
        maxpoints = 0.0
    print(maxpoints)


if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("HIBA: NEM MEGFELELO PARAMETEREZES")
        print(" [TANARI MEGOLDAS] [HALLGATOI MEGOLDAS] [MAX PONTSZAM]")
        exit(1)

    nodeid = 0
    d1 = nx.Graph()
    d2 = nx.Graph()

    # Hallgatói beolvasás
    g1 = Graph.FullGraph(list(), list(), list())
    textanalyze(sys.argv[2], g1, d1)

    # Tanári beolvasása
    # print("\nTanári beolvasása")
    g2 = Graph.FullGraph(list(), list(), list())
    textanalyze(sys.argv[1], g2, d2)

    startged()