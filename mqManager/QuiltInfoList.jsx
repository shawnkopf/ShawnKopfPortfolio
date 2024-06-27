import { Card, List, ListItem, Title } from "@tremor/react";

export default function QuiltInfoList({quilt})
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
        <Card
            decoration={decoration().location}
            decorationColor={decoration().color}
            className="max-w-2xl"
        >
            <Title>Quilt Details</Title>
            <List>
                <ListItem>
                    <span>Quilt Id</span>
                    <span>{quilt?.id ?? ''}</span>
                </ListItem>
                <ListItem>
                    <span>Due Date</span>
                    <span>{quilt?.dueDate ?? ''}</span>
                </ListItem>
                <ListItem>
                    <span>Received Date</span>
                    <span>{quilt?.receivedDate ?? ''}</span>
                </ListItem>
                <ListItem>
                    <span>Location</span>
                    <span>{quilt?.status?.location ?? 'Awaiting Receipt'}</span>
                </ListItem>
                <ListItem>
                    <span>Expedited</span>
                    <span>{quilt?.expedited ?? '' === 1 ? 'True' : 'False'}</span>
                </ListItem>
            </List>
        </Card>
    )
}
