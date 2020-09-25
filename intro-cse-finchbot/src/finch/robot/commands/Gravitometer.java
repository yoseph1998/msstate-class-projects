package finch.robot.commands;

import finch.robot.Robot;
import finch.robot.framework.Command;

import java.util.LinkedList;

/**
 * Created by yosephsa on 10/2/2016.
 */
public class Gravitometer extends Command {

    private LinkedList<Double> xAccelValues;
    private LinkedList<Double> yAccelValues;
    private LinkedList<Double> zAccelValues;

    public Gravitometer() {
        this("Gravitometer");
    }

    public Gravitometer(String name) {
        super(name);
        requires(Robot.getInstance().getDriveTrain());
        setTimeout(10);
    }

    @Override
    protected void init() {
        Robot.getInstance().getWindow().addText(getName() + " Started");
        xAccelValues = new LinkedList<>();
        yAccelValues = new LinkedList<>();
        zAccelValues = new LinkedList<>();
    }

    @Override
    protected void update() {
        if (Robot.getInstance().getFinch().isFinchLevel())
            Robot.getInstance().getDriveTrain().setVelocity(3.6);
        else
            Robot.getInstance().getDriveTrain().setVelocity(0);

        xAccelValues.add(Robot.getInstance().getFinch().getXAcceleration());
        yAccelValues.add(Robot.getInstance().getFinch().getYAcceleration());
        zAccelValues.add(Robot.getInstance().getFinch().getZAcceleration());
    }

    @Override
    protected boolean isFinished() {
        return isTimedOut();
    }

    @Override
    protected void end() {
        Robot.getInstance().getDriveTrain().setVelocity(0);
        Robot.getInstance().getWindow().addText("X Accel Values: " + xAccelValues.toString());
        Robot.getInstance().getWindow().addText("Y Accel Values: " + yAccelValues.toString());
        Robot.getInstance().getWindow().addText("Z Accel Values: " + zAccelValues.toString());
        Robot.getInstance().getWindow().addText(getName() + " Stopped");
    }

    @Override
    protected void interrupt() {
        Robot.getInstance().getDriveTrain().setVelocity(0);
        Robot.getInstance().getWindow().addText(getName() + " Interrupted");
    }
}
