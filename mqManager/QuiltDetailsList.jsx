import { Card, List, ListItem, Title } from "@tremor/react";

export default function QuiltDetailsList({quilt})
{

    const decoration = () => {
        const decorations = {
            location: "",
            color: ""
        };

        if (quilt?.expedited === 1) {
            decorations.location = "top"
            decorations.color = "red"

        }
        return decorations
    }

    return(
        <div>
        <Card
            className="max-w-2xl"
            decoration={decoration().location}
            decorationColor={decoration().color}
        >
            <Title>Quilt Details</Title>
            <List>
                <ListItem>
                    <span>Pattern</span>
                    <span>{quilt?.pattern}</span>
                </ListItem>
                <ListItem>
                    <span>Thread Color</span>
                    <span>{quilt?.thread_color}</span>
                </ListItem>
                <ListItem>
                    <span>Binding</span>
                    <span>{quilt?.binding_notes} {quilt?.has_binding === 1 ? "(Binding Included)" : "(Not Included)"}</span>
                </ListItem>
                <ListItem>
                    <span>Backing</span>
                    <span>{quilt?.backing_included}</span>
                </ListItem>
                <ListItem>
                    <span>Size</span>
                    <span>{quilt?.length}x{quilt?.width}</span>
                </ListItem>
            </List>
        </Card>
    </div>
    )
}
