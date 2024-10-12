package commands

import (
	"api/core/database"
	"api/core/net/sessions"
	eztable "api/core/tables"
	"fmt"
)

func ongoing(session *sessions.Session, args []string) {
	attacks, err := database.Container.GetRunning(session.User)
	if err != nil {
		fmt.Fprintf(session.Conn, "Error: %v\n\r", err)
		return
	}

	if len(attacks) == 0 {
		fmt.Fprintf(session.Conn, "No running attacks\n\r")
		return
	}

	table := eztable.NewTable()

	table.SetStyle(eztable.Unicode)

	table.Cells = append(table.Cells, &eztable.Cell{Title: "ID", Alignment: &eztable.Align{Header: eztable.Left}})
	table.Cells = append(table.Cells, &eztable.Cell{Title: "Host", Alignment: &eztable.Align{Header: eztable.Left}})
	table.Cells = append(table.Cells, &eztable.Cell{Title: "Duration", Alignment: &eztable.Align{Header: eztable.Left}})
	table.Cells = append(table.Cells, &eztable.Cell{Title: "Method", Alignment: &eztable.Align{Header: eztable.Left}})

	for _, attack := range attacks {
		table.Cells[0].Bodys = append(table.Cells[0].Bodys, fmt.Sprintf("%d", attack.ID))
		table.Cells[1].Bodys = append(table.Cells[1].Bodys, attack.Target)
		table.Cells[2].Bodys = append(table.Cells[2].Bodys, fmt.Sprintf("%d", attack.Duration))
		table.Cells[3].Bodys = append(table.Cells[3].Bodys, attack.Method.Name)
	}

	tableString := table.String()

	fmt.Fprint(session.Conn, tableString, "\n", "\r\n")
}
