import requests, json
from flask import Flask, jsonify, request

app = Flask(__name__)

class Resolver:
    @app.route("/GitHub/Royaloakap/cfxResolver/", methods=["GET", "POST"])
    def cfx():
        cfx_code = request.args.get('cfx')
        if cfx_code == None: return jsonify({"Error": "You did not specify CFX Code."})
        elif "HTTP" in cfx_code.upper() or '/' in cfx_code or "." in cfx_code: return jsonify({"Error" : "You need to specify an actual CFX code."})

        headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv102.0) Gecko/20100101 Firefox/102.0"}

        if len(cfx_code) > 7:
            return jsonify({"Error": "Length of CFX Code is long, please try again."})
        try:
            cfxRequest = requests.get(f"https://servers-frontend.fivem.net/api/servers/single/{cfx_code}", headers=headers)
            cfxRequest = json.loads(cfxRequest.text)
        except:
            return jsonify({"Error": "Could not make a GET request to the FiveM API."})
        try:
            if cfxRequest['error'] == "404 Not Found": return jsonify({"Error": "CFX Code is not valid on FiveM End Database."})
        except:
            server_hostname = cfxRequest['Data']['hostname']
            server_owner = cfxRequest['Data']['ownerName']
            server_ip = cfxRequest['Data']['connectEndPoints'][0]
            server_port = cfxRequest['Data']['connectEndPoints'][0].replace(":", " ").split()[1]
            server_json = f"http://{cfxRequest['Data']['connectEndPoints'][0]}/players.json"
            server_players = cfxRequest['Data']['clients']

            x = server_ip.split(":")

            json_format = {
                "Server Name": server_hostname,
                "Server Owner": server_owner,
                "Server IP": x[0],
                "Server Port": server_port,
                "Server Json": server_json,
                "Server Connected Players": server_players
            }

            return jsonify(json_format)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8000)

### By Royaloakap, t.me/ROYAL_FAQ