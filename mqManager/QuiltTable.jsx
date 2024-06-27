import { StatusOnlineIcon, ClockIcon } from "@heroicons/react/outline";

import {
    Card,
    Table,
    TableHead,
    TableRow,
    TableHeaderCell,
    TableBody,
    TableCell,
    Text,
    Title,
    Badge,
} from "@tremor/react";
import Pagination from "@/Components/Pagination.jsx";

export default function QuiltTable({ title, quilts }) {
    const rowStyle = (quilt) => {
        if (quilt.isDangerZone) {
            return "bg-red-100 hover:bg-red-200 cursor-pointer";
        }
        return "hover:bg-sky-50 cursor-pointer";
    };

    const quiltsData = () => {
        if (quilts && quilts.data) {
            return Array.isArray(quilts.data) ? quilts.data : [];
        }
        return [];
    };

    const badge = (quilt) => {
        const active = [
            "received",
            "staged",
            "quilted",
            "trimmed",
            "bound",
            "shipped",
        ];

        const envelopeIcon = () => {
            return (
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth={1.5}
                    stroke="currentColor"
                    className="w-4 h-4 mr-1"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                    />
                </svg>
            );
        };

        if (quilt.last_update == null) {
            return (
                <Badge color="blue" icon={envelopeIcon}>
                    {" "}
                    awaiting receipt
                </Badge>
            );
        }

        if (active.includes(quilt.last_update)) {
            return (
                <Badge color="emerald" icon={StatusOnlineIcon}>
                    {quilt.last_update}
                </Badge>
            );
        }

        return (
            <Badge color="yellow" icon={ClockIcon}>
                {quilt.last_update}
            </Badge>
        );
    };

    return (
        <Card>
            <Title>{title}</Title>
            <Table className="mt-5">
                <TableHead>
                    <TableRow>
                        <TableHeaderCell>Status</TableHeaderCell>
                        <TableHeaderCell>Customer</TableHeaderCell>
                        <TableHeaderCell>Order Number</TableHeaderCell>
                        <TableHeaderCell>Due Date</TableHeaderCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {quiltsData().map((quilt) => (
                        <TableRow
                            onClick={() =>
                                (window.location.href = "/quilts/" + quilt.id)
                            }
                            className={rowStyle(quilt)}
                            key={quilt.id}
                        >
                            <TableCell>{badge(quilt)}</TableCell>
                            <TableCell>
                                <Text>{quilt.customerName}</Text>
                            </TableCell>
                            <TableCell>{quilt.orderName}</TableCell>
                            <TableCell>
                                <Text>{quilt.dueDate}</Text>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            {quilts && quilts.links && (
                <Pagination className="mt-6" links={quilts.links} />
            )}
        </Card>
    );
}
